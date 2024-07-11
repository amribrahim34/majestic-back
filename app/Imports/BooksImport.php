<?php

namespace App\Imports;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class BooksImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $categoryId = $this->findOrCreateCategory($row['category'], $row['subcategory']);
        $publisherId = $this->findOrCreatePublisher($row['publisher']);

        $book = new Book([
            'title' => $row['title'],
            'category_id' => $categoryId ?? null,
            'publisher_id' =>  $publisherId ?? null,
            'publication_date' => $this->parseDate($row['publication_date']) ?? null,
            'language_id' => $row['language_id'] ?? null,
            'isbn10' => $row['isbn10'] ?? null,
            'isbn13' => $row['isbn13'] ?? null,
            'num_pages' => $row['num_pages'] ?? null,
            'dimensions' => $row['dimensions'] ?? null,
            'weight' => $row['weight'] ?? null,
            'format' => $row['format'] ?? 'Hard Copy',
            'price' => $row['price'] ?? null,
            'stock_quantity' => $row['stock_quantity'] ?? null,
            'description' => $row['description'] ?? null,
            'img' => $row['img'] ?? null,
        ]);

        $book->save();

        // Handle multiple authors
        $authorNames = explode(',', $row['author']);
        foreach ($authorNames as $authorName) {
            $authorId = $this->findOrCreateAuthor(trim($authorName));
            if ($authorId) {
                $book->authors()->attach($authorId);
            }
        }

        return $book;
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            if (preg_match('/^\d{4}$/', $date)) {
                return Carbon::createFromFormat('Y', $date)->startOfYear()->format('Y-m-d');
            }
        }

        return null;
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
        $mainCategory = Category::firstOrCreate(
            ['category_name' => $subjectName],
            [
                'category_name' => $subjectName,
                'parent_id' => null
            ]
        );

        $subCategory = Category::firstOrCreate(
            ['category_name' => $subSubjectName, 'parent_id' => $mainCategory->id],
            [
                'category_name' => $subSubjectName,
                'parent_id' => $mainCategory->id
            ]
        );

        return $subCategory->id;
    }

    private function findOrCreatePublisher($publisherName)
    {
        return Publisher::firstOrCreate(
            ['publisher_name' => $publisherName],
            ['publisher_name' => $publisherName]
        )->id;
    }
}
