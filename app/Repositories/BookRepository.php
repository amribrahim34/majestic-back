<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;


class BookRepository implements BookRepositoryInterface
{
    public function all()
    {
        $limit = request()->query->get('limit', 10);
        return Book::with(['authors', 'category', 'publisher', 'language'])->paginate($limit);
    }

    public function findById($id)
    {
        return Book::with(['authors', 'category', 'publisher', 'language'])->findOrFail($id);
    }

    public function create(array $data)
    {
        if (isset($data['img']) && $data['img'] instanceof UploadedFile) {
            $path = $data['img']->store('book_images', 'public');
            $data['img'] = $path;
        }
        $data['publication_date'] = Carbon::parse($data['publication_date'])->format('Y-m-d H:i:s');
        return Book::create($data);
    }

    public function update($id, array $data)
    {
        $book = $this->findById($id);
        if (isset($data['img']) && $data['img'] instanceof UploadedFile) {
            $path = $data['img']->store('book_images', 'public');
            $data['img'] = $path;
        }
        if (isset($data['publication_date'])) {
            $data['publication_date'] = Carbon::parse($data['publication_date'])->format('Y-m-d H:i:s');
        }
        $book->update($data);
        return $book->load(['authors', 'category', 'publisher', 'language']);
    }

    public function delete($id)
    {
        $book = $this->findById($id);
        $book->delete();
    }

    public function importImagesFromCsv($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: $filePath");
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $updateData = $this->prepareUpdateData($records);

        if (!empty($updateData)) {
            $this->batchUpdateImages($updateData);
        }
    }

    private function prepareUpdateData($records): array
    {
        $updateData = [];

        foreach ($records as $record) {
            $isbn = $record['isbn'] ?? null;
            $image = $record['image'] ?? null;

            if (!$isbn || !$image) {
                Log::warning("Skipping row due to missing data", $record);
                continue;
            }

            $updateData[] = [
                'isbn' => $isbn,
                'image' => $image,
            ];
        }

        return $updateData;
    }

    private function batchUpdateImages(array $updateData)
    {
        $cases = [];
        $isbns = [];
        $images = [];

        foreach ($updateData as $data) {
            $cases[] = "WHEN isbn10 = ? OR isbn13 = ? THEN ?";
            $isbns[] = $data['isbn'];
            $isbns[] = $data['isbn'];
            $images[] = $data['image'];
        }

        $casesString = implode(' ', $cases);
        $params = array_merge($isbns, $images);

        $affected = Book::whereRaw("isbn10 IN ('" . implode("','", $isbns) . "') OR isbn13 IN ('" . implode("','", $isbns) . "')")
            ->update(['img' => DB::raw("CASE $casesString END")], $params);

        Log::info("Updated $affected books with new image data");

        return $affected;
    }
}
