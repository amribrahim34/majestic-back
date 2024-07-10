<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Website\BookRepositoryInterface;
use Illuminate\Http\Request;

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
        return response()->json($books);
    }

    public function show($id)
    {
        $book = $this->bookRepository->getBookById($id);
        return response()->json($book);
    }

    public function byCategory($categoryId)
    {
        $books = $this->bookRepository->getBooksByCategory($categoryId);
        return response()->json($books);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $filters = $this->getFiltersFromRequest($request);
        $books = $this->bookRepository->searchBooks($query, $filters);
        return response()->json($books);
    }

    public function latest(Request $request)
    {
        $limit = $request->get('limit', 10);
        $books = $this->bookRepository->getLatestBooks($limit);
        return response()->json($books);
    }

    public function bestSellers(Request $request)
    {
        $limit = $request->get('limit', 10);
        $books = $this->bookRepository->getBestSellingBooks($limit);
        return response()->json($books);
    }

    private function getFiltersFromRequest(Request $request): array
    {
        return $request->only([
            'category',
            'format',
            'price_min',
            'price_max',
            'publishing_year',
            'publisher',
            'author'
        ]);
    }
}
