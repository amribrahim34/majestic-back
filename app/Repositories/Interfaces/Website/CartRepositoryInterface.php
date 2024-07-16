<?php

namespace App\Repositories\Interfaces\Website;

interface CartRepositoryInterface
{
    public function getCart($userId);
    public function addItem($userId, $bookId, $quantity);
    public function updateItemQuantity($userId, $bookId, $quantity);
    public function removeItem($userId, $bookId);
    public function clearCart($userId);
    public function getCartTotal($userId);
    public function transferGuestCart($sessionId, $userId);
}
