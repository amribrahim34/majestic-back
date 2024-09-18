<?php

namespace App\Imports;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessImport;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class BooksImport  implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts, ShouldQueue
{

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $rows->each(function ($row) {
                // Log::alert('this is the row', $row->toArray());
                if (empty($row['title'])) {
                    Log::error('Skipping row due to missing title', $row->toArray());
                    return; // Skip this row
                }
                $categoryId = $this->findOrCreateCategory($row['category'], $row['subcategory']);
                $publisherId = $this->findOrCreatePublisher($row['publisher']);

                $data = [
                    'title' => $row['title'],
                    'category_id' => $categoryId ?? null,
                    'publisher_id' =>  $publisherId ?? null,
                    'publication_date' => $this->parseDate($row['publication_date']) ?? null,
                    'language_id' => $row['language_id'] ?? 1,
                    'isbn10' => $this->cleanIsbn($row['isbn10'] ?? null),
                    'isbn13' => $this->cleanIsbn($row['isbn13'] ?? null),
                    'num_pages' => $row['num_pages'] ?? null,
                    'dimensions' => $row['dimensions'] ?? null,
                    'weight' => $row['weight'] ?? null,
                    'format' => $row['format'] ?? 'Hard Copy',
                    'price' => $this->cleanPrice($row['price'] ?? null),
                    'stock_quantity' => $row['stock_quantity'] ?? 10,
                    'description' => $row['description'] ?? null,
                    'img' => $row['img'] ?? null,
                    'sort' => $row['sort'] ?? null,
                    'is_active' => $row['is_active'] ?? null,
                ];
                Log::alert('import books', $data);
                $book =  Book::create($data);
                $authorNames = $this->parseAuthors($row['author']);
                foreach ($authorNames as $authorName) {
                    $authorId = $this->findOrCreateAuthor($authorName);
                    if ($authorId) {
                        $book->authors()->attach($authorId);
                    }
                }
            });
        });
    }

    public function headingRow(): int
    {
        return 1; // Assuming the first row is the header
    }



    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'publisher_id' => 'sometimes|exists:publishers,id',
            'publication_date' => 'sometimes|date',
            'language_id' => 'required|exists:languages,id',
            'isbn10' => 'sometimes|string|size:10',
            'isbn13' => 'sometimes|string|size:13',
            'num_pages' => 'sometimes|integer|min:1',
            'dimensions' => 'sometimes|string',
            'weight' => 'sometimes|numeric|min:0',
            'format' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'description' => 'sometimes|string',
            'img' => 'sometimes|string',
        ];
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    public function getCsvSettings(): array
    {
        return [
            // 'delimiter' => ' ',  // Set the delimiter to space
            'enclosure' => '"',
            'input_encoding' => 'UTF-8',
        ];
    }



    private function cleanIsbn($isbn)
    {
        if (!$isbn) {
            return null;
        }

        $cleanIsbn = preg_replace('/\D/', '', $isbn);
        if (strlen($cleanIsbn) == 10 || strlen($cleanIsbn) == 13) {
            return $cleanIsbn;
        }
        return null;
    }

    private function cleanPrice($price)
    {
        if (empty($price)) {
            return 0; // or null, depending on your database schema
        }
        $cleanPrice = preg_replace('/[^0-9.]/', '', $price);
        return $cleanPrice === '' ? 0 : (float)$cleanPrice;
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // If it's just a year, create a date for January 1st of that year
            if (preg_match('/^\d{4}$/', $date)) {
                return Carbon::createFromFormat('Y', $date)->startOfYear()->format('Y-m-d');
            }
            // Otherwise, try to parse the date normally
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            // If parsing fails, log the error and return null
            Log::error("Failed to parse date: " . $date . ". Error: " . $e->getMessage());
            return null;
        }
    }

    private function parseAuthors($authorString)
    {
        // Split authors by ' - ' or ' ، '
        $authors = preg_split('/\s*-\s*|\s*،\s*/', $authorString);

        // Remove any leading 'د/' or 'أ.د.' from each author name
        $authors = array_map(function ($author) {
            return preg_replace('/^(د\/|أ\.د\.|د\.|أ\.|أ د\/)?\s*/', '', trim($author));
        }, $authors);

        return array_filter($authors); // Remove any empty elements
    }

    private function findOrCreateAuthor($authorName)
    {
        if (empty(trim($authorName))) {
            return null;
        }

        $name = trim($authorName);

        $author = Author::firstOrCreate(
            ['name' => $name],
            ['name' => $name]
        );

        return $author->id;
    }

    private function findOrCreateCategory($subjectName, $subSubjectName)
    {
        if (empty($subjectName)) {
            return null;
        }

        $mainCategory = Category::firstOrCreate(
            ['category_name' => $subjectName],
            ['category_name' => $subjectName]
        );

        if (empty($subSubjectName)) {
            return $mainCategory->id;
        }

        $subCategory = Category::firstOrCreate(
            ['category_name' => $subSubjectName, 'parent_id' => $mainCategory->id],
            ['category_name' => $subSubjectName, 'parent_id' => $mainCategory->id]
        );

        return $subCategory->id;
    }

    private function findOrCreatePublisher($publisherName)
    {
        if (empty($publisherName)) {
            return null;
        }

        return Publisher::firstOrCreate(
            ['publisher_name' => $publisherName],
            ['publisher_name' => $publisherName]
        )->id;
    }
}
