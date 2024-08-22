<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesOverviewRequest;
use App\Repositories\Interfaces\Admin\Dashboard\SalesRepositoryInterface;
use Illuminate\Http\JsonResponse;

class SalesOverviewController extends Controller
{
    protected $salesRepository;

    public function __construct(SalesRepositoryInterface $salesRepository)
    {
        $this->salesRepository = $salesRepository;
    }

    public function index(SalesOverviewRequest $request): JsonResponse
    {
        $period = $request->input('period', 'monthly'); // Default to monthly if not provided
        $periodMap = [
            'daily' => '1 day',
            'weekly' => '1 week',
            'monthly' => '1 month',
            '30days' => '30 days',
            'quarterly' => '3 months',
            'yearly' => '1 year',
        ];

        // Check if the provided period exists in the map, otherwise default to monthly
        $mappedPeriod = $periodMap[$period] ?? $periodMap['monthly'];

        $totalRevenue = $this->salesRepository->getTotalRevenue();
        $numberOfOrders = $this->salesRepository->getNumberOfOrders();
        $averageOrderValue = $this->salesRepository->getAverageOrderValue();
        $salesTrend = $this->salesRepository->getSalesTrend($mappedPeriod);

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => $totalRevenue,
                'number_of_orders' => $numberOfOrders,
                'average_order_value' => $averageOrderValue,
                'sales_trend' => $salesTrend,
            ],
            'message' => __('messages.sales_overview_retrieved_successfully'),
        ]);
    }
}
