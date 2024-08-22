<?php

namespace App\Imports;

use App\Jobs\ProcessImport;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;

class CategoryUpdate implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, ShouldQueue
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $book = Category::find($row['category_id']);

        if (!$book) {
            return null;
        }

        $fillableFields = [
            'category_name',
            'parent_id',
            'description',
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
            'category_id' => 'required|exists:categories,id',
            'category_name' => 'sometimes|string|max:255',
            'parent_id' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
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
