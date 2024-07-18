<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\Website\BlogPostIndexRequest;
use App\Http\Requests\Website\BlogPostShowRequest;
use App\Http\Resources\Website\BlogPostResource;
use App\Repositories\Interfaces\Website\BlogPostRepositoryInterface;
use Illuminate\Http\JsonResponse;

class BlogPostController extends Controller
{
    protected $repository;

    public function __construct(BlogPostRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(BlogPostIndexRequest $request): JsonResponse
    {
        $posts = $this->repository->getAllPublished();
        return response()->json(BlogPostResource::collection($posts));
    }

    public function show(BlogPostShowRequest $request, string $slug): JsonResponse
    {
        $post = $this->repository->getPublishedBySlug($slug);

        if (!$post) {
            return response()->json(['message' => __('blog.post_not_found')], 404);
        }

        return response()->json(new BlogPostResource($post));
    }

    public function recent(BlogPostIndexRequest $request): JsonResponse
    {
        $posts = $this->repository->getRecentPublished($request->input('limit', 5));
        return response()->json(BlogPostResource::collection($posts));
    }

    public function byTag(BlogPostIndexRequest $request, string $tagSlug): JsonResponse
    {
        $posts = $this->repository->getPublishedByTag($tagSlug);
        return response()->json(BlogPostResource::collection($posts));
    }
}
