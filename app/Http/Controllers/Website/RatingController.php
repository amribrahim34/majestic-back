<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRatingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateRatingRequest;
use App\Models\Book;
use App\Models\Rating;
use App\Repositories\Interfaces\Website\BookRatingRepositoryInterface;

class RatingController extends Controller
{

    private $bookRatingRepository;

    public function __construct(BookRatingRepositoryInterface $bookRatingRepository)
    {
        $this->bookRatingRepository = $bookRatingRepository;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRatingRequest $request)
    {
        $v = $request->validated();
        $book = Book::find($v['book_id']);
    }



    public function rateBook(Request $request, $bookId): JsonResponse
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
        ]);

        $this->bookRatingRepository->rateBook($bookId, $request->rating);

        return response()->json(['message' => 'Book rated successfully']);
    }

    public function getUserRating($bookId): JsonResponse
    {
        $userId = auth('sanctum')->id();
        $rating = $this->bookRatingRepository->getUserRatingForBook($bookId, $userId);

        return response()->json(['rating' => $rating]);
    }

    public function updateRating(Request $request, $bookId): JsonResponse
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
        ]);

        $userId = auth('sanctum')->id();
        $this->bookRatingRepository->updateRating($bookId, $userId, $request->rating);

        return response()->json(['message' => 'Rating updated successfully']);
    }

    public function deleteRating($bookId): JsonResponse
    {
        $userId = auth('sanctum')->id();
        $this->bookRatingRepository->deleteRating($bookId, $userId);

        return response()->json(['message' => 'Rating deleted successfully']);
    }
}
