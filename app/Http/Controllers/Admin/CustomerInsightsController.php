<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerInsightsRequest;
use App\Repositories\Interfaces\Admin\Dashboard\CustomerInsightsRepositoryInterface;
use Illuminate\Http\JsonResponse;

class CustomerInsightsController extends Controller
{
    protected $customerInsightsRepository;

    public function __construct(CustomerInsightsRepositoryInterface $customerInsightsRepository)
    {
        $this->customerInsightsRepository = $customerInsightsRepository;
    }

    public function index(CustomerInsightsRequest $request): JsonResponse
    {
        $period = $request->input('period');
        $topCustomersLimit = $request->input('top_customers_limit', 10);

        $periodMap = [
            '30days' => '30 days',
            '6months' => '6 months',
            '1year' => '1 year',
        ];

        $newVsReturning = $this->customerInsightsRepository->getNewVsReturningCustomers($periodMap[$period]);
        $geographicDistribution = $this->customerInsightsRepository->getCustomerGeographicDistribution();
        $topCustomers = $this->customerInsightsRepository->getTopCustomersByRevenue($topCustomersLimit);

        return response()->json([
            'success' => true,
            'data' => [
                'new_vs_returning' => $newVsReturning,
                'geographic_distribution' => $geographicDistribution,
                'top_customers' => $topCustomers,
            ],
            'message' => __('messages.customer_insights_retrieved_successfully'),
        ]);
    }
}
