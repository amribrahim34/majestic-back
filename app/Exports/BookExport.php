<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\Queue\ShouldQueue;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class BookExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, ShouldAutoSize, ShouldQueue
{
    public function query()
    {
        return Book::query()->with(['category:id,category_name', 'publisher:id,publisher_name', 'language:id,language_name']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Category',
            'Publisher',
            'Publication Date',
            'Language',
            'ISBN-10',
            'ISBN-13',
            'Number of Pages',
            'Dimensions',
            'Weight',
            'Format',
            'Price',
            'Stock Quantity',
            'Description',
            'Image URL',
            'Order Count',
            'Average Rating',
        ];
    }

    public function map($book): array
    {
        return [
            $book->id,
            $this->sanitizeString($book->title),
            $this->sanitizeString($book->category->category_name ?? 'N/A'),
            $this->sanitizeString($book->publisher->publisher_name ?? 'N/A'),
            $book->publication_date,
            $this->sanitizeString($book->language->language_name ?? 'N/A'),
            $this->sanitizeString($book->isbn10),
            $this->sanitizeString($book->isbn13),
            $book->num_pages,
            $this->sanitizeString($book->dimensions),
            $this->sanitizeString($book->weight),
            $this->sanitizeString($book->format),
            $book->price,
            $book->stock_quantity,
            $this->sanitizeString($book->description),
            $this->sanitizeString($book->img),
            $book->order_count,
            $book->average_rating,
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function sanitizeString($value): string
    {
        // Remove any leading equals sign to prevent formula injection
        $value = ltrim($value, '=');

        // Escape any other characters that could be interpreted as formulas
        $value = StringHelper::sanitizeUTF8($value);

        // Limit the length of the string to prevent issues with very long text
        return mb_substr($value, 0, 32000);
    }
}
