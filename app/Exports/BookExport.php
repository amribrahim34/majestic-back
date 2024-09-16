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
use Illuminate\Support\Facades\DB;

class BookExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, ShouldAutoSize, ShouldQueue
{
    public function query()
    {
        return Book::query()
            ->select(
                'books.id',
                'books.title',
                'books.isbn10',
                'books.isbn13',
                'books.price',
                'books.img',
                'categories.category_name',
                'publishers.publisher_name',
                'languages.language_name'
            )
            ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
            ->leftJoin('publishers', 'books.publisher_id', '=', 'publishers.id')
            ->leftJoin('languages', 'books.language_id', '=', 'languages.id');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Category',
            'Publisher',
            'Language',
            'ISBN-10',
            'ISBN-13',
            'Price',
            'Image URL',
        ];
    }

    public function map($book): array
    {
        return [
            $book->id,
            $this->sanitizeString($book->title),
            $this->sanitizeString($book->category_name ?? 'N/A'),
            $this->sanitizeString($book->publisher_name ?? 'N/A'),
            $this->sanitizeString($book->language_name ?? 'N/A'),
            $this->sanitizeString($book->isbn10),
            $this->sanitizeString($book->isbn13),
            $book->price,
            $this->sanitizeString($book->img),
        ];
    }

    public function chunkSize(): int
    {
        return 500; // Reduced chunk size
    }

    private function sanitizeString($value): string
    {
        $value = is_string($value) ? $value : (string)$value;
        $value = ltrim($value, '=');
        $value = StringHelper::sanitizeUTF8($value);
        return mb_substr($value, 0, 32000);
    }
}
