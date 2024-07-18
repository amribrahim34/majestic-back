<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'keywords',
        'is_published',
        'img',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_post_tag');
    }
}
