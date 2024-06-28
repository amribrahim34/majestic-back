<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Http\UploadedFile;

class BookRepository implements BookRepositoryInterface
{
    public function all()
    {
        return Book::with(['author', 'category', 'publisher', 'language'])->paginate();
    }

    public function findById($id)
    {
        return Book::findOrFail($id);
    }

    public function create(array $data)
    {
        if (isset($data['img']) && $data['img'] instanceof UploadedFile) {
            $path = $data['img']->store('book_images', 'public');
            $data['img'] = $path;
        }

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
