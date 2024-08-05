<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Repositories\Interfaces\Website\OrderRepositoryInterface;

class OrderController extends Controller
{

    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = $this->orderRepository->getAllOrders();
        return response()->json(["data" => $orders]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $this->createOrUpdateAddress();
            $order = $this->orderRepository->makeOrder();
        } catch (\Throwable $th) {
            throw $th;
        }
        return response()->json([
            __('orders.created'),
            $order
        ]);
    }

    public function show(int $orderId)
    {
        $order = $this->orderRepository->getOrder($orderId);
        if (!$order) {
            return response()->json(['message' => __('orders.not_found')], 404);
        }
        return response()->json(['data' => $order]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        // Assuming you want to update the order status or other details
        // You might need to add a method to the repository for this
        // $updatedOrder = $this->orderRepository->updateOrder($order->id, $request->validated());
        // return response()->json(['message' => __('orders.updated'), 'data' => $updatedOrder]);

        // For now, we'll just return a placeholder response
        return response()->json(['message' => __('orders.updated')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $cancelled = $this->orderRepository->cancelOrder($order->id);
        if ($cancelled) {
            return response()->json(['message' => __('orders.cancelled')], 200);
        }
        return response()->json(['message' => __('orders.cancel_failed')], 400);
    }

    /**
     * Trace the specified order.
     */
    public function trace(int $orderId)
    {
        $traceInfo = $this->orderRepository->traceOrder($orderId);
        return response()->json(['data' => $traceInfo]);
    }

    /**
     * Refund the specified order.
     */
    public function refund(int $orderId)
    {
        $refunded = $this->orderRepository->refundOrder($orderId);
        if ($refunded) {
            return response()->json(['message' => __('orders.refunded')], 200);
        }
        return response()->json(['message' => __('orders.refund_failed')], 400);
    }


    private function createOrUpdateAddress()
    {
        $user = auth('sanctum')->user();

        $addressData = request()->only([
            'city',
            'address',
            'latitude',
            'longitude',
            'special_mark',
            'phone'
        ]);

        $address = $user->addresses()->updateOrCreate(
            ['is_default' => true],
            array_merge($addressData, ['is_default' => true])
        );

        return $address;
    }
}
