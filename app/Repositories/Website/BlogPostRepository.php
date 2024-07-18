<?php

namespace App\Repositories\Website;

use App\Models\BlogPost;
use App\Models\Tag;
use App\Repositories\Interfaces\Website\BlogPostRepositoryInterface;

class BlogPostRepository implements BlogPostRepositoryInterface
{
    /**
     * @var BlogPost
     */
    protected $model;

    /**
     * BlogPostRepository constructor.
     *
     * @param BlogPost $model
     */
    public function __construct(BlogPost $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function getAllPublished()
    {
        return $this->model->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->with('tags')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getPublishedBySlug(string $slug)
    {
        return $this->model->where('is_published', true)
            ->where('slug', $slug)
            ->with('tags')
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function getRecentPublished(int $limit = 5)
    {
        return $this->model->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getPublishedByTag(string $tagSlug)
    {
        $tag = Tag::where('slug', $tagSlug)->firstOrFail();

        return $tag->blogPosts()
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
