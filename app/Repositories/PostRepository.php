<?php

namespace App\Repositories;

use App\Models\BlogPost;
use App\Repositories\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{
    public function paginated()
    {
        $limit = request()->limit ?? 10;
        return BlogPost::paginate($limit);
    }

    public function findById($id)
    {
        return BlogPost::findOrFail($id);
    }

    public function create($data)
    {
        return BlogPost::create($data);
    }

    public function edit($post, $data)
    {
        $post->update($data);
        return $post;
    }

    public function delete($post)
    {
        $post->delete();
        return;
    }
}
