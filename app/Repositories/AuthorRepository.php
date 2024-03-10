<?php

// app/Repositories/AuthorRepository.php

namespace App\Repositories;

use App\Models\Author;
use App\Repositories\Interfaces\AuthorRepositoryInterface;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function all()
    {
        return Author::all();
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
