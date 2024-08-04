<?php

namespace App\Repositories\Website;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Repositories\Interfaces\Website\OrderRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use amribrahim34\BostaEgypt\Endpoints\Pricing;


class OrderRepository implements OrderRepositoryInterface
{
    private $userId;
    private $pricing;


    public function __construct(Pricing $pricing)
    {
        $this->userId = auth('sanctum')->id();
        $this->pricing = $pricing;
    }

    public function makeOrder(): array
    {
        $user = auth('sanctum')->user();
        $address = $user->defaultAddress;
        return DB::transaction(function () use ($address) {
            $cart = $this->getUserCart();

            $this->validateCart($cart);

            $totalAmount = $this->calculateTotalAmount($cart);

            // Calculate shipment cost based on division
            $shipmentCost = $this->calculateShipmentCost($totalAmount, $address->city);

            // Add shipment cost to the total amount

            $order = $this->createOrder($totalAmount);

            $this->createOrderItems($order, $cart);

            $this->clearCart();

            return $order->load('items')->toArray();
        });
    }

    protected function calculateShipmentCost($orderTotal, $city)
    {
        $params = [
            'cod' => $orderTotal,
            'dropOffCity' => $city,
            'pickupCity' => 'Cairo',
            'size' => 'Normal',
            'type' => 'SEND'
        ];

        $response = $this->pricing->calculateShipment($params);

        if ($response['status'] === 'success') {
            return $response['data']['cost'];
        }

        throw new \Exception('Failed to calculate shipment cost.');
    }


    private function getUserCart()
    {
        return Cart::where('user_id', $this->userId)->with('items.book')->first();
    }

    private function validateCart($cart): void
    {
        if (!$cart || $cart->items->isEmpty()) {
            throw new Exception('Cart is empty');
        }
    }

    private function calculateTotalAmount($cart): float
    {
        return $cart->items->sum(function ($item) {
            return $item->quantity * $item->book->price;
        });
    }

    private function createOrder(float $totalAmount): Order
    {
        return Order::create([
            'user_id' => $this->userId,
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);
    }

    private function createOrderItems(Order $order, $cart): void
    {
        foreach ($cart->items as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'book_id' => $cartItem->book_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->book->price,
                'title' => $cartItem->book->title,
                'isbn13' => $cartItem->book->isbn13,
            ]);

            // Decrease stock quantity
            // $cartItem->book->decrement('stock_quantity', $cartItem->quantity);
        }
    }

    public function getAllOrders(int $userId): array
    {
        return Order::with('items')
            ->where('user_id', $userId)
            ->get()
            ->toArray();
    }

    public function getOrder(int $orderId): ?array
    {
        $order = Order::with('items')->find($orderId);
        return $order ? $order->toArray() : null;
    }

    public function traceOrder(int $orderId): array
    {
        return Order::findOrFail($orderId)->toArray();
    }

    public function refundOrder(int $orderId): bool
    {
        return $this->updateOrderStatus($orderId, 'refunded');
    }

    public function cancelOrder(int $orderId): bool
    {
        return $this->updateOrderStatus($orderId, 'canceled');
    }

    private function updateOrderStatus(int $orderId, string $newStatus): bool
    {
        $order = Order::findOrFail($orderId);

        if ($order->status === 'out_for_delivery') {
            return false;
        }

        $order->status = $newStatus;
        return $order->save();
    }

    private function clearCart(): void
    {
        $cart = Cart::where('user_id', $this->userId)->first();
        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }
    }

    public function getStates()
    {
        $egypt = country('eg');
        $states = $egypt->getDivisions();
        return  $states;
    }

    public function getCities($division)
    {
        $egypt = country('eg');
        $cities = $egypt->getDivision($division);
        return  $cities;
    }
}
