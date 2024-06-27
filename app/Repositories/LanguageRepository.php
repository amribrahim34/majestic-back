<?php
// app/Repositories/CategoryRepository.php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Language;
use App\Repositories\Interfaces\LanguageRepositoryInterface;
use Illuminate\Support\Facades\App;

class LanguageRepository implements LanguageRepositoryInterface
{
    public function all()
    {
        return Language::all();
    }

    public function findById($id)
    {
        return Language::findOrFail($id);
    }
}
