<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CategoryExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Category::query()->with('parent');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Category Name',
            'Parent Category',
            'Description',
            'Created At',
            'Updated At',
        ];
    }

    public function map($category): array
    {
        return [
            $category->id,
            $category->category_name,
            $category->parent ? $category->parent->category_name : 'None',
            $category->description,
            $category->created_at,
            $category->updated_at,
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
