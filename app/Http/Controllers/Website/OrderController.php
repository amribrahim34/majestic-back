<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\Website\Order\OrderResource;
use App\Models\Order;
use App\Repositories\Interfaces\Website\OrderRepositoryInterface;
use Illuminate\Http\JsonResponse;

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
    public function index(): JsonResponse
    {
        $orders = $this->orderRepository->getAllOrders();
        return response()->json(["data" => OrderResource::collection($orders)]);
    }


    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $address = $this->createOrUpdateAddress();
            $order = $this->orderRepository->makeOrder($address);
            return response()->json([
                'message' => __('orders.created'),
                'data' => new OrderResource($order)
            ], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => __('orders.create_failed')], 500);
        }
    }


    public function show(int $orderId): JsonResponse
    {
        $order = $this->orderRepository->getOrder($orderId);
        if (!$order) {
            return response()->json(['message' => __('orders.not_found')], 404);
        }
        return response()->json(['data' => new OrderResource($order)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        // $updatedOrder = $this->orderRepository->updateOrder($order->id, $request->validated());
        return response()->json([
            'message' => __('orders.updated'),
            // 'data' => new OrderResource($updatedOrder)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): JsonResponse
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
    public function trace(int $orderId): JsonResponse
    {
        $traceInfo = $this->orderRepository->traceOrder($orderId);
        return response()->json(['data' => $traceInfo]);
    }


    /**
     * Refund the specified order.
     */
    public function refund(int $orderId): JsonResponse
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
