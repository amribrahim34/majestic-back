<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\Admin\AuthorResource;
use App\Models\Author;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use Illuminate\Http\Response;

class AuthorController extends Controller
{

    protected $authors;

    public function __construct(AuthorRepositoryInterface $authors)
    {
        $this->authors = $authors;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = $this->authors->all();
        return AuthorResource::collection($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request)
    {
        $author = $this->authors->create($request->validated());
        return response()->json([
            'data' => new AuthorResource($author),
            'message' => __('authors.created')
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        // $author = $this->authors->findById($id);
        return new AuthorResource($author);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, Author $author)
    {
        $author = $this->authors->update($author->id, $request->validated());
        return response()->json([
            'data' => new AuthorResource($author),
            'message' => __('authors.updated')
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        $this->authors->delete($author->id);
        return response()->json(['message' => __('authors.deleted')], Response::HTTP_NO_CONTENT);
    }
}
