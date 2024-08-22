<?php


namespace App\Repositories\Admin\Dashboard;

use App\Models\Order;
use App\Repositories\Interfaces\Admin\Dashboard\OrderStatusRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderStatusRepository implements OrderStatusRepositoryInterface
{
    public function getRecentOrders($limit = 10)
    {
        return Order::with(['user:id,user_name', 'items:id,order_id,book_id,quantity,price'])
            ->orderBy('order_date', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getOrdersByStatus()
    {
        return Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
    }
}
