<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\Author;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AuthorExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Author::query();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Biography',
            'Birth Date',
            'Death Date',
            'Country',
            'Created At',
            'Updated At',
        ];
    }

    public function map($author): array
    {
        return [
            $author->id,
            $author->name,
            $author->biography,
            $author->birth_date,
            $author->death_date,
            $author->country,
            $author->created_at,
            $author->updated_at,
        ];
    }
}
