<?php

namespace App\Imports;

use App\Jobs\ProcessImport;
use App\Models\Book;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookUpdate implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, ShouldQueue
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $book = Book::find($row['book_id']);

        if (!$book) {
            return null;
        }

        $fillableFields = [
            'title',
            'category_id',
            'publisher_id',
            'publication_date',
            'language_id',
            'isbn10',
            'isbn13',
            'num_pages',
            'dimensions',
            'weight',
            'format',
            'price',
            'stock_quantity',
            'description',
            'img'
        ];

        $data = array_intersect_key($row, array_flip($fillableFields));
        $data = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });

        $book->update($data);
        $job = $this->job;
        if ($job instanceof ProcessImport) {
            $progress = ($job->getJobBatchId() / $this->totalRows) * 100;
            $job->updateProgress($progress);
        }
        return $book;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'book_id' => 'required|exists:books,id',
            'title' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'publisher_id' => 'sometimes|exists:publishers,id',
            'publication_date' => 'sometimes|date',
            'language_id' => 'sometimes|exists:languages,id',
            'isbn10' => 'sometimes|string|size:10',
            'isbn13' => 'sometimes|string|size:13',
            'num_pages' => 'sometimes|integer|min:1',
            'dimensions' => 'sometimes|string',
            'weight' => 'sometimes|numeric|min:0',
            'format' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
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
}
