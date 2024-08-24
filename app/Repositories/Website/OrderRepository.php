<?php

namespace App\Repositories\Website;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Repositories\Interfaces\Website\OrderRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use amribrahim34\BostaEgypt\BostaApi;
use App\Models\Address;
use Illuminate\Support\Facades\Log;

class OrderRepository implements OrderRepositoryInterface
{
    private $userId;
    private $bostaApi;


    public function __construct(BostaApi $bostaApi)
    {
        $this->userId = auth('sanctum')->id();
        $this->bostaApi = $bostaApi;
    }

    public function makeOrder()
    {
        return DB::transaction(function () {
            $cart = $this->getUserCart();
            $city_name = request()->city;
            $this->validateCart($cart);
            $totalAmount = $this->calculateTotalAmount($cart);
            // $shipmentCost = $this->calculateShipmentCost($totalAmount, $city_name);
            $shipmentCost = 100;
            $totalAmount += $shipmentCost;


            // Update user data if provided
            $this->updateUserData();

            // Create or update user address
            $address = $this->createOrUpdateAddress();


            $order = $this->createOrder($totalAmount, $shipmentCost);
            $this->createOrderItems($order, $cart);
            $this->clearCart();
            return $order->load('items');
        });
    }

    private function updateUserData()
    {
        $user = auth('sanctum')->user();
        $updatableFields = ['user_name', 'email', 'mobile', 'gender', 'birthday'];

        $userData = request()->only($updatableFields);

        if (!empty($userData)) {
            $user->update($userData);
        }
    }

    private function createOrUpdateAddress()
    {
        $user = auth('sanctum')->user();
        $addressData = request()->only(['address', 'city', 'country', 'postal_code']);

        if (empty($addressData)) {
            throw new Exception('Address information is required');
        }

        $address = $user->addresses()->updateOrCreate(
            ['user_id' => $user->id],
            $addressData
        );

        return $address;
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

        $response = $this->bostaApi->pricing->calculateShipment($params);

        // Log the response for debugging
        // Log::info('Bosta API Response:', ['response' => $response['data']]);

        // $decodedResponse = json_decode($response, true);

        if (is_array($response) && $response['success'] && isset($response['data']['priceAfterVat'])) {
            return $response['data']['priceAfterVat'];
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

    private function createOrder(float $totalAmount, $shipping)
    {
        $user = auth('sanctum')->user();
        $address = $user->addresses->first();
        Log::alert([$address->address]);
        return Order::create([
            'user_id' => $this->userId,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'shipping_address' => $address->address,
            'shipping_cost' => $shipping,
            'city' => $address->city,
            "country" => "Egypt",
            'order_date' => now(),
        ]);
    }

    private function createOrderItems(Order $order, $cart)
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

    public function getAllOrders()
    {
        return Order::with('items')
            ->where('user_id', $this->userId)
            ->paginate();
    }

    public function getOrder(int $orderId)
    {
        $order = Order::with('items.book')->find($orderId);
        Log::alert("this is the order", [$order]);
        return $order ? $order : null;
    }

    public function traceOrder(int $orderId)
    {
        return Order::findOrFail($orderId);
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
