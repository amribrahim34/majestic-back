<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Website\BookRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    private $bookRepository;

    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function index(Request $request)
    {
        $filters = $this->getFiltersFromRequest($request);
        $books = $this->bookRepository->getAllBooks($filters);
        return BookResource::collection($books);
    }

    public function show($id)
    {
        $book = $this->bookRepository->getBookById($id);
        $rating = $this->bookRepository->getBookRating($id);
        $relatedProducts = $this->bookRepository->getRelatedProducts($id);

        return (new BookResource($book))
            ->additional([
                'rating' => $rating,
                'related_products' => BookResource::collection($relatedProducts),
            ]);
    }

    public function byCategory($categoryId)
    {
        $books = $this->bookRepository->getBooksByCategory($categoryId);
        return BookResource::collection($books);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $filters = $this->getFiltersFromRequest($request);
        $books = $this->bookRepository->searchBooks($query, $filters);
        return BookResource::collection($books);
    }

    public function latest(Request $request)
    {
        $limit = $request->get('limit', 10);
        $books = $this->bookRepository->getLatestBooks($limit);
        return BookResource::collection($books);
    }

    public function bestSellers(Request $request)
    {
        $limit = $request->get('limit', 10);
        $books = $this->bookRepository->getBestSellingBooks($limit);
        return BookResource::collection($books);
    }

    private function getFiltersFromRequest(Request $request): array
    {
        $filters = [];

        if ($request->has('category_ids')) {
            $filters['category_ids'] = is_array($request->input('category_ids'))
                ? $request->input('category_ids')
                : explode(',', $request->input('category_ids'));
        }

        if ($request->has('formats')) {
            $filters['formats'] = is_array($request->input('formats'))
                ? $request->input('formats')
                : [$request->input('formats')];
        }

        if ($request->has('price_range')) {
            $priceRange =  explode(',', $request->input('price_range')[0]);
            $filters['price_min'] = $priceRange[0] ?? null;
            $filters['price_max'] = $priceRange[1] ?? null;
        }

        if ($request->has('year_range')) {
            $yearRange =  explode(',', $request->input('year_range')[0]);
            $filters['year_min'] = $yearRange[0] ?? null;
            $filters['year_max'] = $yearRange[1] ?? null;
        }

        if ($request->has('search')) {
            $filters['search'] = $request->input('search');
        }
        Log::debug("filters in controller", $request->all());

        return $filters;
    }
    public function getPriceRange()
    {
        return $this->bookRepository->getPriceRange();
    }

    public function getYearRange()
    {
        return $this->bookRepository->getYearRange();
    }

    public function getFormats()
    {
        return $this->bookRepository->getDistinctFormats();
    }
}
