<?php

namespace App\Repositories\Website;

use App\Models\Book;
use App\Repositories\Interfaces\Website\BookRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;


class BookRepository implements BookRepositoryInterface
{
    public function getAllBooks(array $filters = [])
    {
        return Book::with(['author', 'category', 'publisher', 'language'])->paginate(20);
    }

    public function getBookById($id)
    {
        return Book::with(['author', 'category', 'publisher', 'language'])->findOrFail($id);
    }

    public function getBooksByCategory($categoryId)
    {
        return Book::where('category_id', $categoryId)
            ->with(['author', 'publisher', 'language'])
            ->paginate(20);
    }

    public function searchBooks($query, array $filters = [])
    {
        return Book::where('title', 'like', "%{$query}%")
            ->orWhereHas('author', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->with(['author', 'category', 'publisher', 'language'])
            ->paginate(20);
    }

    public function getLatestBooks($limit = 10)
    {
        return Book::with(['author', 'category', 'publisher', 'language'])
            ->orderBy('publication_date', 'desc')
            ->take($limit)
            ->get();
    }

    public function getBestSellingBooks($limit = 10)
    {
        // Assuming you have a sales or order table to determine best-selling books
        // This is a placeholder implementation
        return Book::with(['author', 'category', 'publisher', 'language'])
            ->orderBy('stock_quantity', 'asc')  // As a simple proxy for popularity
            ->take($limit)
            ->get();
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->whereIn('name', $filters['category']);
            });
        }

        if (isset($filters['format'])) {
            $query->whereIn('format', $filters['format']);
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (isset($filters['publishing_year'])) {
            $query->whereYear('publication_date', $filters['publishing_year']);
        }

        if (isset($filters['publisher'])) {
            $query->whereHas('publisher', function ($q) use ($filters) {
                $q->whereIn('name', $filters['publisher']);
            });
        }

        if (isset($filters['author'])) {
            $query->whereHas('author', function ($q) use ($filters) {
                $q->whereIn('name', $filters['author']);
            });
        }

        return $query;
    }
}
