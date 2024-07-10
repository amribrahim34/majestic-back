<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Website\BookRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;

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
        return new BookResource($book);
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
