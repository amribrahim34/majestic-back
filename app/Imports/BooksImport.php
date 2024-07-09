<?php

namespace App\Imports;

use App\Models\Author;
use Illuminate\Support\Collection;
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
        $authorId = $this->findOrCreateAuthor($row['author']);
        $categoryId = $this->findOrCreateCategory($row['category'], $row['subcategory']);
        $publisherId = $this->findOrCreatePublisher($row['publisher']);

        return new Book([
            'title' => $row['title'],
            'author_id' => $authorId ?? null,
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
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        // Try parsing as a full date
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            // If parsing fails, check if it's just a year
            if (preg_match('/^\d{4}$/', $date)) {
                return Carbon::createFromFormat('Y', $date)->startOfYear()->format('Y-m-d');
            }
        }

        // If all parsing attempts fail, return null
        return null;
    }

    private function findOrCreateAuthor($authorName)
    {
        // Check if the author name is empty
        if (empty(trim($authorName))) {
            return null;
        }

        // Split the author name
        $nameParts = explode(' ', trim($authorName));

        // If there's only one part, assume it's the last name
        if (count($nameParts) == 1) {
            $lastName = $nameParts[0];
            $firstName = ''; // Set to empty string instead of null
        } else {
            // Assume the last part is the last name
            $lastName = array_pop($nameParts);

            // If there are still parts, assume the first is the first name and the rest (if any) are the middle name
            $firstName = array_shift($nameParts);
            $middleName = !empty($nameParts) ? implode(' ', $nameParts) : null;
        }

        // Try to find the author
        $author = Author::where('last_name', $lastName)
            ->where('first_name', $firstName)
            ->first();

        // If not found, create a new author
        if (!$author) {
            $author = Author::create([
                'first_name' => $firstName,
                'middle_name' => $middleName ?? null,
                'last_name' =>  $lastName,
            ]);
        }

        return $author->id;
    }

    private function findOrCreateCategory($subjectName, $subSubjectName)
    {
        // Find or create the main category
        $mainCategory = Category::firstOrCreate(
            ['category_name' => $subjectName],
            [
                'category_name' => $subjectName,
                'parent_id' => null
            ]
        );

        // Find or create the sub-category
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
            [
                'publisher_name' => $publisherName,
            ]
        )->id;
    }
}
