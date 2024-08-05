<?php

namespace App\Repositories\Interfaces\Website;

interface OrderRepositoryInterface
{
    public function makeOrder(): array;
    // public function removeItemFromCart(int $userId, int $itemId): bool;
    public function getAllOrders(): array;
    public function getOrder(int $orderId): ?array;
    public function traceOrder(int $orderId): array;
    public function refundOrder(int $orderId): bool;
    public function cancelOrder(int $orderId): bool;
}
