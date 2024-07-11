<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'biography',
        'birth_date',
        'death_date',
        'country',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }
}
