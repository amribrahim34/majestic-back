<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];

    // Specify which attributes are translatable.
    // public $translatable = ['title', 'description'];


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

    public function getImageAttribute()
    {
        if ($this->img) {
            return asset('storage/book_covers/' . $this->img);
        }
        return asset('storage/book_covers/default.png');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
