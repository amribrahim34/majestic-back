<?php

namespace App\Repositories\Interfaces\Website;

interface WishListRepositoryInterface
{
    public function getWishList($userId);
    public function addItem($userId, $bookId);
    public function removeItem($userId, $bookId);
    public function clearWishList($userId);
    public function isItemInWishList($userId, $bookId);
}
