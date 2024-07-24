<?php

namespace App\Imports;

use App\Models\Book;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;

class BooksImageImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function collection(Collection $rows)
    {
        $updateData = [];

        foreach ($rows as $row) {
            $isbn = $row['isbn'];
            $image = $row['image'];

            if (!$isbn || !$image) {
                Log::warning("Skipping row due to missing data", $row->toArray());
                continue;
            }

            $updateData[] = [
                'isbn' => $isbn,
                'image' => $image,
            ];
        }

        if (!empty($updateData)) {
            $this->batchUpdate($updateData);
        }
    }

    private function batchUpdate(array $updateData)
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
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
