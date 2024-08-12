<?php

namespace App\Repositories\Admin;

use App\Models\Book;
use App\Repositories\Interfaces\Admin\BookRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class BookRepository implements BookRepositoryInterface
{
    public function all()
    {
        $query = Book::with(['authors', 'category', 'publisher', 'language']);
        $this->applyFilters($query);
        $this->applySorting($query);
        return $query->paginate($this->getLimit());
    }

    public function findById($id)
    {
        return Book::with(['authors', 'category', 'publisher', 'language'])->findOrFail($id);
    }

    public function create(array $data)
    {
        $data = $this->processImageUpload($data);
        $data['publication_date'] = $this->formatDate($data['publication_date']);
        return Book::create($data);
    }

    public function update($id, array $data)
    {
        $book = $this->findById($id);
        $data = $this->processImageUpload($data);
        if (isset($data['publication_date'])) {
            $data['publication_date'] = $this->formatDate($data['publication_date']);
        }
        $book->update($data);
        return $book->load(['authors', 'category', 'publisher', 'language']);
    }

    public function delete($id)
    {
        $this->findById($id)->delete();
    }

    public function importImagesFromCsv($filePath)
    {
        $this->validateFile($filePath);
        $records = $this->readCsvRecords($filePath);
        $updateData = $this->prepareUpdateData($records);
        if (!empty($updateData)) {
            return $this->batchUpdateImages($updateData);
        }
        return 0;
    }

    private function applyFilters($query)
    {
        $filters = [
            'search' => [$this, 'applySearchFilter'],
            'category_id' => [$this, 'applyCategoryFilter'],
            'publisher_id' => [$this, 'applyPublisherFilter'],
            'author_id' => [$this, 'applyAuthorFilter'],
            'min_price' => [$this, 'applyMinPriceFilter'],
            'max_price' => [$this, 'applyMaxPriceFilter'],
        ];

        foreach ($filters as $param => $callback) {
            $value = request()->query($param);
            if ($value) {
                $callback($query, $value);
            }
        }
    }

    private function applySearchFilter($query, $search)
    {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('authors', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        });
    }

    private function applyCategoryFilter($query, $categoryId)
    {
        $query->where('category_id', $categoryId);
    }

    private function applyPublisherFilter($query, $publisherId)
    {
        $query->where('publisher_id', $publisherId);
    }

    private function applyAuthorFilter($query, $authorId)
    {
        $query->whereHas('authors', function ($q) use ($authorId) {
            $q->where('authors.id', $authorId);
        });
    }

    private function applyMinPriceFilter($query, $minPrice)
    {
        $query->where('price', '>=', $minPrice);
    }

    private function applyMaxPriceFilter($query, $maxPrice)
    {
        $query->where('price', '<=', $maxPrice);
    }

    private function applySorting($query)
    {
        $sortBy = request()->query('sort_by', 'created_at');
        $sortOrder = request()->query('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
    }

    private function getLimit()
    {
        return request()->query('limit', 10);
    }

    private function processImageUpload(array $data)
    {
        if (isset($data['img']) && $data['img'] instanceof UploadedFile) {
            $data['img'] = $data['img']->store('book_images', 'public');
        }
        return $data;
    }

    private function formatDate($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    private function validateFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: $filePath");
        }
    }

    private function readCsvRecords($filePath)
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        return $csv->getRecords();
    }

    private function prepareUpdateData($records): array
    {
        $updateData = [];
        foreach ($records as $record) {
            if (!isset($record['isbn'], $record['image'])) {
                Log::warning("Skipping row due to missing data", $record);
                continue;
            }
            $updateData[] = [
                'isbn' => $record['isbn'],
                'image' => $record['image'],
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
