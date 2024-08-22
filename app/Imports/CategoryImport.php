<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class CategoryImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, ShouldQueue
{
    private $parentCategories;

    public function __construct()
    {
        $this->parentCategories = collect();
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        Category::firstOrCreate(
            ['category_name' => $row['category_name']],
            [
                'parent_id' => $row['parent_id'] ?? null,
                'description' => $row['description'] ?? null,
            ]
        );
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'category_name' => 'required|string|max:255',
            'parent_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
