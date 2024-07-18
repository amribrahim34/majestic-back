<?php

namespace App\Repositories\Interfaces\Website;

interface BlogPostRepositoryInterface
{
    /**
     * Get all published blog posts.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPublished();

    /**
     * Get a single published blog post by slug.
     *
     * @param string $slug
     * @return \App\Models\BlogPost|null
     */
    public function getPublishedBySlug(string $slug);

    /**
     * Get recent published blog posts.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentPublished(int $limit = 5);

    /**
     * Get published blog posts by tag.
     *
     * @param string $tagSlug
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPublishedByTag(string $tagSlug);
}
