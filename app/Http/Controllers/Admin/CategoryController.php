<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class CategoryController extends Controller
{

    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $language = $request->get('lang', app()->getLocale()); // Default to app locale
        $categories = $this->categoryRepository->findByLanguage($language);
        return response()->json(['data' => $categories]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        // $category = Category::create($request->validated());
        $category = $this->categoryRepository->create($request->validated());

        return response()->json([
            'message' => __('categories.created'),
            'data' => new CategoryResource($category)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->categoryRepository->update($category->id, $request->validated());

        return response()->json([
            'message' => __('categories.updated'),
            'data' => new CategoryResource($category)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->categoryRepository->delete($category->id);

        return response()->json(['message' => __('categories.deleted')], Response::HTTP_NO_CONTENT);
    }
}
