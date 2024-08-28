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
use App\Imports\CategoryUpdate;
use Maatwebsite\Excel\Facades\Excel;

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

    public function export()
    {
        return Excel::download(new CategoryExport, 'categories.xlsx');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB file
        ]);

        $file = $request->file('file');

        try {
            Excel::import(new CategoryImport, $file);

            return response()->json([
                'message' => 'Categories imported successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error importing categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateFromExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt',
        ]);

        $file = $request->file('file');

        try {
            Excel::import(new CategoryUpdate, $file);

            return response()->json(['message' => 'Category update process started successfully'], 202);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error processing file: ' . $e->getMessage()], 500);
        }
    }
}
