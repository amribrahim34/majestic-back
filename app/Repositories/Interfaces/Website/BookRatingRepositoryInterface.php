<?php

namespace App\Repositories\Interfaces\Website;

interface BookRatingRepositoryInterface
{
    public function rateBook(int $bookId, int $rating): void;
    public function getUserRatingForBook(int $bookId, int $userId): ?int;
    public function updateRating(int $bookId, int $userId, int $rating): void;
    public function deleteRating(int $bookId, int $userId): void;
}
