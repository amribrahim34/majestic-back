<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Publisher extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'publisher_name',
        'logo',
        'location',
        'website',
    ];

    public $translatable = ['publisher_name'];
}
