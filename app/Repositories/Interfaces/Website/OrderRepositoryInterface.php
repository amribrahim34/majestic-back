<?php

namespace App\Repositories\Interfaces\Website;

interface OrderRepositoryInterface
{
    public function makeOrder();
    // public function removeItemFromCart(int $userId, int $itemId): bool;
    public function getAllOrders();
    public function getOrder(int $orderId);
    public function traceOrder(int $orderId);
    public function refundOrder(int $orderId): bool;
    public function cancelOrder(int $orderId): bool;
}
