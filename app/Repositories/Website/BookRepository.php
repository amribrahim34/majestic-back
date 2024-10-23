<?php

namespace App\Repositories\Website;

use App\Models\Book;
use App\Repositories\Interfaces\Website\BookRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookRepository implements BookRepositoryInterface
{

    public function getAllBooks(array $filters = [])
    {
        $query = Book::with(['authors', 'category', 'publisher', 'language'])->isActive();


        $query = $query->orderBy(DB::raw('ISNULL(sort), sort'), 'ASC');

        $query = $this->applyFilters($query, $filters);
        return $query->paginate(9);
    }

    public function getBookById($id)
    {
        return Book::with(['authors', 'category', 'publisher', 'language'])->findOrFail($id);
    }

    public function getBooksByCategory($categoryId)
    {
        return Book::where('category_id', $categoryId)
            ->with(['authors', 'publisher', 'language'])
            ->isActive()
            ->orderBy(DB::raw('ISNULL(sort), sort'), 'ASC')
            ->paginate(20);
    }

    // public function searchBooks($query, array $filters = [])
    // {
    //     return Book::where('title', 'like', "%{$query}%")
    //         ->orWhere('isbn10', $query)
    //         ->orWhere('isbn13', $query)
    //         // ->whereNotNull('img')
    //         ->orWhere('publication_date', $query)
    //         ->orWhereHas('authors', function ($q) use ($query) {
    //             $q->where('name', 'like', "%{$query}%");
    //         })
    //         ->orWhereHas('category', function ($q) use ($query) {
    //             $q->where('category_name', 'like', "%{$query}%");
    //         })
    //         ->orWhereHas('publisher', function ($q) use ($query) {
    //             $q->where('name', 'like', "%{$query}%");
    //         })
    //         ->with(['authors', 'category', 'publisher', 'language'])
    //         ->paginate(20);
    // }

    public function searchBooks($query, array $filters = [])
    {
        $searchQuery = Book::query();

        // Split the query into words
        $keywords = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);

        $searchQuery->where(function ($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $q->orWhere(function ($subQuery) use ($keyword) {
                    $this->addFuzzySearchConditions($subQuery, $keyword);
                });
            }
        });

        // Apply filters
        $searchQuery = $this->applyFilters($searchQuery, $filters);

        // Eager load relationships
        $searchQuery->with(['authors', 'category', 'publisher', 'language']);

        // Log the final SQL query for debugging
        Log::debug("Search Query SQL", [$searchQuery->toSql(), $searchQuery->getBindings()]);

        return $searchQuery->paginate(20);
    }

    private function addFuzzySearchConditions($query, $keyword)
    {
        $fields = ['title', 'isbn10', 'isbn13'];
        $relatedFields = [
            'authors' => 'name',
            'category' => 'category_name',
            'publisher' => 'name'
        ];

        foreach ($fields as $field) {
            $query->orWhere($field, 'like', "%{$keyword}%")
                ->orWhereRaw("LEVENSHTEIN(?, {$field}) <= ?", [$keyword, $this->calculateMaxDistance($keyword)]);
        }

        foreach ($relatedFields as $relation => $field) {
            $query->orWhereHas($relation, function ($q) use ($field, $keyword) {
                $q->where($field, 'like', "%{$keyword}%")
                    ->orWhereRaw("LEVENSHTEIN(?, {$field}) <= ?", [$keyword, $this->calculateMaxDistance($keyword)]);
            });
        }
    }

    private function calculateMaxDistance($keyword)
    {
        return min(3, strlen($keyword) - 1);
    }

    public function getLatestBooks($limit = 10)
    {
        return Book::with(['authors', 'category', 'publisher', 'language'])
            ->whereNotNull('img')
            ->isActive()
            ->orderBy(DB::raw('ISNULL(sort), sort'), 'ASC')
            ->take($limit)
            ->get();
    }

    public function getBestSellingBooks($limit = 10)
    {
        // Assuming you have a sales or order table to determine best-selling books
        // This is a placeholder implementation
        return Book::with(['authors', 'category', 'publisher', 'language'])
            ->whereNotNull('img')
            ->take($limit)
            ->get();
    }

    public function getBookRating($id)
    {
        $book = Book::findOrFail($id);
        return [
            'average_rating' => $book->average_rating,
            'ratings_count' => $book->ratings()->count(),
        ];
    }

    public function getRelatedProducts($id, $limit = 5)
    {
        $book = Book::findOrFail($id);

        return Book::where('category_id', $book->category_id)
            ->where('id', '!=', $id)
            ->isActive()
            ->orderBy(DB::raw('ISNULL(sort), sort'), 'ASC')
            ->limit($limit)
            ->get();
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['category_ids'])) {
            $query->whereIn('category_id', $filters['category_ids'])
                ->orWhereHas('category', function ($q) use ($filters) {
                    $q->whereIn('parent_id', $filters['category_ids']);
                });
        }

        if (isset($filters['formats'])) {
            $query->whereIn('format', $filters['formats']);
        }

        if (isset($filters['price_min'])) {
            $query->where('price', '>=', floatval($filters['price_min']));
        }

        if (isset($filters['price_max'])) {
            $query->where('price', '<=', floatval($filters['price_max']));
        }

        if (isset($filters['year_min'])) {
            $query->whereYear('publication_date', '>=', intval($filters['year_min']));
        }

        if (isset($filters['year_max'])) {
            $query->whereYear('publication_date', '<=', intval($filters['year_max']));
        }

        if (isset($filters['search'])) {
            $searchTerm = $filters['search'];
            $searchResults = Book::search($searchTerm)->raw();
            $bookIds = collect($searchResults['hits'])->pluck('id')->toArray();

            if (!empty($bookIds)) {
                $query->whereIn('id', $bookIds);
            } else {
                $query->where('id', 0);
            }
        }
        Log::debug("filters in repository", $filters);
        Log::debug("Query SQL", [$query->toSql(), $query->getBindings()]);
        Log::debug("Result count before pagination: " . $query->count());
        return $query;
    }

    public function getPriceRange()
    {
        $minPrice = Book::min('price');
        $maxPrice = Book::max('price');

        return [
            'min' => is_numeric($minPrice) ? (float)$minPrice : 0,
            'max' => is_numeric($maxPrice) ? (float)$maxPrice : 0
        ];
    }

    public function getYearRange()
    {
        $minYear = Book::min('publication_date');
        $maxYear = Book::max('publication_date');

        return [
            'min' => is_numeric($minYear) ? (int)date('Y', strtotime($minYear)) : 0,
            'max' => is_numeric($maxYear) ? (int)date('Y', strtotime($maxYear)) : date('Y')
        ];
    }

    public function getDistinctFormats()
    {
        return Book::distinct('format')->pluck('format');
    }
}
