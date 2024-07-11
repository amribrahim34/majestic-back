<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;


class BookRepository implements BookRepositoryInterface
{
    public function all()
    {
        $limit = request()->query->get('limit', 10);
        return Book::with(['authors', 'category', 'publisher', 'language'])->paginate($limit);
    }

    public function findById($id)
    {
        return Book::with(['authors', 'category', 'publisher', 'language'])->findOrFail($id);
    }

    public function create(array $data)
    {
        if (isset($data['img']) && $data['img'] instanceof UploadedFile) {
            $path = $data['img']->store('book_images', 'public');
            $data['img'] = $path;
        }
        $data['publication_date'] = Carbon::parse($data['publication_date'])->format('Y-m-d H:i:s');
        return Book::create($data);
    }

    public function update($id, array $data)
    {
        $book = $this->findById($id);
        if (isset($data['img']) && $data['img'] instanceof UploadedFile) {
            $path = $data['img']->store('book_images', 'public');
            $data['img'] = $path;
        }
        if (isset($data['publication_date'])) {
            $data['publication_date'] = Carbon::parse($data['publication_date'])->format('Y-m-d H:i:s');
        }
        $book->update($data);
        return $book->load(['authors', 'category', 'publisher', 'language']);
    }

    public function delete($id)
    {
        $book = $this->findById($id);
        $book->delete();
    }
}
