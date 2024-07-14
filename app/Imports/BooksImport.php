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

        $authorNames = $this->parseAuthors($row['author']);
        foreach ($authorNames as $authorName) {
            $authorId = $this->findOrCreateAuthor($authorName);
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
        return Publisher::firstOrCreate(
            ['publisher_name' => $publisherName],
            ['publisher_name' => $publisherName]
        )->id;
    }
}
