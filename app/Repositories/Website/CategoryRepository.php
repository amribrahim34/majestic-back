<?php

namespace App\Repositories\Website;

use App\Models\Category;
use App\Repositories\Interfaces\Website\CategoryRepositoryInterface;


class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories()
    {
        return Category::withCount('books')->get();
    }
}
