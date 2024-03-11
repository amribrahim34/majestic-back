<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Book extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'title',
        'author_id',
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
    ];

    // Specify which attributes are translatable.
    public $translatable = ['title', 'description'];



    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
