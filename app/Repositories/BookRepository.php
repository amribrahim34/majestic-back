<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;

class BookRepository implements BookRepositoryInterface
{
    public function all()
    {
        return Book::all();
    }

    public function findById($id)
    {
        return Book::findOrFail($id);
    }

    public function create(array $data)
    {
        // Assuming $data includes properly formatted arrays for title and description per Spatie's requirements
        return Book::create($data);
    }

    public function update($id, array $data)
    {
        $book = $this->findById($id);
        $book->update($data);
        return $book;
    }

    public function delete($id)
    {
        $book = $this->findById($id);
        $book->delete();
    }
}
