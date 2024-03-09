<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::paginate();
        return CategoryResource::collection($categories);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        // $category = Category::create($request->validated());
        $category = new Category();
        $category->setTranslations('category_name', $request->category_name);
        $category->setTranslations('description', $request->description ?: []);
        $category->parent_id = $request->parent_id;
        $category->save();

        $data =  new CategoryResource($category);
        return response([
            'message' =>  __('categories.created'),
            'data' => $data
        ], 201);
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
        // $category->update($request->validated());
        $category->setTranslations('category_name', $request->category_name);
        $category->setTranslations('description', $request->description ?: []);
        $category->parent_id = $request->parent_id;
        $category->save();

        $data =  new CategoryResource($category);
        return response([
            'message' =>  __('categories.updated'),
            'data' => $data
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response([
            'message' =>  __('categories.deleted'),
        ], 201);
    }
}
