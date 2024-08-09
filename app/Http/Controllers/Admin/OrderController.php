<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\Admin\OrderCollection;
use App\Http\Resources\Admin\OrderResource;
use App\Mail\NewOrderMail;
use App\Notifications\NewOrderNotification;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{

    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Get all orders",
     *     @OA\Response(response="200", description="List of orders")
     * )
     */
    public function index(): JsonResponse
    {
        $orders = $this->orderRepository->all();
        Log::alert('order ', [new OrderCollection($orders)]);
        return response()->json(new OrderCollection($orders), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new order",
     *     @OA\Response(response="201", description="Order created successfully")
     * )
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderRepository->create($request->validated());

        // Send notifications
        $this->sendOrderNotifications($order);

        return response()->json(new OrderResource($order), 201, [
            'message' => __('orders.created_successfully')
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get a specific order",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Order details")
     * )
     */
    public function show($id): JsonResponse
    {
        $order = $this->orderRepository->findWithItems($id);
        return response()->json(new OrderResource($order), 200);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}/status",
     *     summary="Update order status",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Order status updated successfully")
     * )
     */
    public function updateStatus(UpdateOrderRequest $request, $id): JsonResponse
    {
        $order = $this->orderRepository->updateStatus($id, $request->status);
        return response()->json(new OrderResource($order), 200, [
            'message' => __('orders.status_updated')
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/revenue",
     *     summary="Get total revenue",
     *     @OA\Parameter(name="start_date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="end_date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Response(response="200", description="Total revenue")
     * )
     */
    public function revenue(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $revenue = $this->orderRepository->getTotalRevenue($startDate, $endDate);
        return response()->json(['revenue' => $revenue], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/top-customers",
     *     summary="Get top customers",
     *     @OA\Response(response="200", description="List of top customers")
     * )
     */
    public function topCustomers(): JsonResponse
    {
        $topCustomers = $this->orderRepository->getMostValuableCustomers();
        return response()->json($topCustomers, 200);
    }

    private function sendOrderNotifications($order)
    {
        // Send Slack notification
        Notification::route('slack', config('services.slack.webhook_url'))
            ->notify(new NewOrderNotification($order));

        // Send email notification
        Mail::to($order->user->email)->send(new NewOrderMail($order));
    }
}
