<?php
// app/Repositories/CategoryRepository.php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\App;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        return Category::all();
    }

    public function findById($id)
    {
        return Category::findOrFail($id);
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update($id, array $data)
    {
        $category = Category::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
    }

    public function findByLanguage($languageCode)
    {
        App::setLocale($languageCode);
        return Category::all()->map(function ($category) use ($languageCode) {
            $category->category_name = $category->getTranslation('category_name', $languageCode);
            $category->description = $category->getTranslation('description', $languageCode);
            return $category;
        });
    }
}
