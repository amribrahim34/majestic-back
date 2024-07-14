<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'parent_id',
        'description',
    ];

    // public $translatable = ['category_name', 'description']; // Specify the attributes you want to be translatable


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function books()
    {
        return $this->hasMany(Book::class, 'category_id');
    }
}
