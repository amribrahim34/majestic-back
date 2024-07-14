<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Website\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        return $this->categoryRepository->getAllCategories();
    }
}
