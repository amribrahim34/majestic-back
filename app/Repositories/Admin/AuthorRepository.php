<?php

// app/Repositories/AuthorRepository.php

namespace App\Repositories\Admin;

use App\Models\Author;
use App\Repositories\Interfaces\Admin\AuthorRepositoryInterface;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function all()
    {
        $limit = request()->limit;
        return Author::paginate($limit);
    }

    public function findById($id)
    {
        return Author::findOrFail($id);
    }

    public function create(array $data)
    {
        return Author::create($data);
    }

    public function update($id, array $data)
    {
        $author = $this->findById($id);
        $author->update($data);
        return $author;
    }

    public function delete($id)
    {
        $author = $this->findById($id);
        $author->delete();
    }
}
