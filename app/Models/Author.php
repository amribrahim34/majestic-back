<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Author extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'biography',
        'birth_date',
        'death_date',
        'country',
    ];

    public $translatable = [
        'first_name',
        'middle_name',
        'last_name',
        'biography',
    ];
}
