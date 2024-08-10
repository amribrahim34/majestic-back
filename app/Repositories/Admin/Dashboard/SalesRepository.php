<?php


namespace App\Repositories\Admin;

use App\Models\Order;
use App\Repositories\Interfaces\Admin\Dashboard\SalesRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesRepository implements SalesRepositoryInterface
{
    public function getTotalRevenue()
    {
        return Order::sum('total_amount');
    }

    public function getNumberOfOrders()
    {
        return Order::count();
    }

    public function getAverageOrderValue()
    {
        return Order::avg('total_amount');
    }

    public function getSalesTrend($period)
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->sub($period);

        return Order::select(
            DB::raw('DATE(order_date) as date'),
            DB::raw('SUM(total_amount) as total_revenue'),
            DB::raw('COUNT(*) as order_count')
        )
            ->whereBetween('order_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
