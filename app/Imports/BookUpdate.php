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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class BookUpdate  implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts, ShouldQueue
{

    private $fillableFields = [
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
        'img',
        'is_active',
        'sort',
    ];
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $rows->each(function ($row) {
                $bookId = $row['book_id'];
                if (!$bookId) {
                    return; // Skip this row if book_id is not present
                }

                $data = $row->only($this->fillableFields)->toArray();
                $data = array_filter($data, fn($value) => $value !== null && $value !== '');

                Book::where('id', $bookId)->update($data);
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

    public function getCsvSettings(): array
    {
        return [
            // 'delimiter' => ' ',  // Set the delimiter to space
            'enclosure' => '"',
            'input_encoding' => 'UTF-8',
        ];
    }
}
