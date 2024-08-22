<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStatusRequest;
use App\Repositories\Interfaces\Admin\Dashboard\OrderStatusRepositoryInterface;
use Illuminate\Http\JsonResponse;

class OrderStatusController extends Controller
{
    protected $orderStatusRepository;

    public function __construct(OrderStatusRepositoryInterface $orderStatusRepository)
    {
        $this->orderStatusRepository = $orderStatusRepository;
    }

    public function index(OrderStatusRequest $request): JsonResponse
    {
        $recentOrdersLimit = $request->input('recent_orders_limit', 10);

        $recentOrders = $this->orderStatusRepository->getRecentOrders($recentOrdersLimit);
        $ordersByStatus = $this->orderStatusRepository->getOrdersByStatus();

        return response()->json([
            'success' => true,
            'data' => [
                'recent_orders' => $recentOrders,
                'orders_by_status' => $ordersByStatus,
            ],
            'message' => __('messages.order_status_retrieved_successfully'),
        ]);
    }
}
