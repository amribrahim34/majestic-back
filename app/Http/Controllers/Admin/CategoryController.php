<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkDeleteCategoriesRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Repositories\Interfaces\Admin\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exports\CategoryExport;
use App\Imports\CategoryImport;


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
        $categories = $this->categoryRepository->all();
        return response()->json([
            'data' => CategoryResource::collection($categories)
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
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

    public function bulkDelete(BulkDeleteCategoriesRequest $request)
    {
        $v = $request->validated();
        $this->categoryRepository->bulkDelete($v);
        return response()->json(['message' => __('categories.deleted')], Response::HTTP_NO_CONTENT);
    }
}
