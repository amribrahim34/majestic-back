<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlogPostRequest;
use App\Http\Requests\UpdateBlogPostRequest;
use App\Http\Resources\Admin\PostResource;
use App\Models\BlogPost;
use App\Repositories\Interfaces\PostRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BlogPostController extends Controller
{

    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = $this->postRepository->paginated();
        return response()->json([
            'data' => PostResource::collection($posts),
            'message' => __('posts.fetched')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogPostRequest $request)
    {
        $post = $this->postRepository->create($request->validated());
        return response()->json([
            'data' => new PostResource($post),
            'message' => __('posts.created')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogPost $blogPost)
    {
        // $post = $this->postRepository->findById($id);
        return response()->json([
            'data' => new PostResource($blogPost),
            'message' => __('posts.fetched')
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost)
    {
        $post = $this->postRepository->edit($blogPost, $request->validated());
        return response()->json([
            'data' => new PostResource($post),
            'message' => __('posts.updated')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blogPost)
    {
        $this->postRepository->delete($blogPost);
        return response()->json(['message' => __('posts.deleted')], 200);
    }

    public function bulkDelete(Request $request)
    {
        $v = $request->validated();
        $this->postRepository->bulkDelete($v);
        return response()->json(['message' => __('posts.deleted')], Response::HTTP_NO_CONTENT);
    }
}
