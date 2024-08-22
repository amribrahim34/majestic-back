<?php

namespace App\Repositories\Admin\Dashboard;

use App\Models\User;
use App\Models\Order;
use App\Repositories\Interfaces\Admin\Dashboard\CustomerInsightsRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerInsightsRepository implements CustomerInsightsRepositoryInterface
{
    public function getNewVsReturningCustomers($period)
    {
        $startDate = Carbon::now()->sub($period);

        $newCustomers = User::where('created_at', '>=', $startDate)->count();
        $returningCustomers = Order::select('user_id')
            ->where('order_date', '>=', $startDate)
            ->whereHas('user', function ($query) use ($startDate) {
                $query->where('created_at', '<', $startDate);
            })
            ->groupBy('user_id')
            ->get()
            ->count();

        return [
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers
        ];
    }

    public function getCustomerGeographicDistribution()
    {
        return Order::select('country', DB::raw('count(distinct user_id) as customer_count'))
            ->groupBy('country')
            ->orderByDesc('customer_count')
            ->get();
    }

    public function getTopCustomersByRevenue($limit = 10)
    {
        return Order::select('user_id', DB::raw('SUM(total_amount) as total_revenue'))
            ->with('user:id,user_name,email')
            ->groupBy('user_id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }
}
