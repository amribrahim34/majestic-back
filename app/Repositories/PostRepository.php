<?php

namespace App\Repositories;

use App\Models\BlogPost;
use App\Repositories\Interfaces\PostRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

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
        if (isset($data['img']) && $data['img'] instanceof UploadedFile) {
            $path = $data['img']->store('post_images', 'public');
            $data['img'] = $path;
        }
        Log::notice('Uploaded file', ['data' => $data]);
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

    public function bulkDelete($params)
    {
        BlogPost::whereIn('id', $params['postIds'])->delete();
    }
}
