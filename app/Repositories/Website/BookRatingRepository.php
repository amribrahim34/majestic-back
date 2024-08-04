<?php

namespace App\Repositories\Website;

use App\Models\Book;
use App\Models\Rating;
use App\Repositories\Interfaces\Website\BookRatingRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BookRatingRepository implements BookRatingRepositoryInterface
{
    public $user_id;

    public function __construct()
    {
        $this->user_id = auth('sanctum')->id();
    }

    public function rateBook(int $bookId, int $rating): void
    {
        $this->validateRating($rating);

        Rating::updateOrCreate(
            ['book_id' => $bookId, 'user_id' => $this->user_id],
            ['rating' => $rating]
        );
    }

    public function getUserRatingForBook(int $bookId, int $userId): ?int
    {
        return Rating::where('book_id', $bookId)
            ->where('user_id', $userId)
            ->value('rating');
    }

    public function updateRating(int $bookId, int $userId, int $rating): void
    {
        $this->validateRating($rating);

        Rating::where('book_id', $bookId)
            ->where('user_id', $userId)
            ->update(['rating' => $rating]);
    }

    public function deleteRating(int $bookId, int $userId): void
    {
        Rating::where('book_id', $bookId)
            ->where('user_id', $userId)
            ->delete();
    }

    private function validateRating(int $rating): void
    {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }
    }
}
