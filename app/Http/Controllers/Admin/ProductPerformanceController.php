<?php


// app/Http/Controllers/ProductPerformanceController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductPerformanceRequest;
use App\Repositories\Interfaces\Admin\Dashboard\ProductPerformanceRepositoryInterface;
use Illuminate\Http\JsonResponse;

class ProductPerformanceController extends Controller
{
    protected $productPerformanceRepository;

    public function __construct(ProductPerformanceRepositoryInterface $productPerformanceRepository)
    {
        $this->productPerformanceRepository = $productPerformanceRepository;
    }

    public function index(ProductPerformanceRequest $request): JsonResponse
    {
        $topSellingLimit = $request->input('top_selling_limit', 10);
        $lowStockThreshold = $request->input('low_stock_threshold', 10);

        $topSellingProducts = $this->productPerformanceRepository->getTopSellingProducts($topSellingLimit);
        $productsWithLowStock = $this->productPerformanceRepository->getProductsWithLowStock($lowStockThreshold);
        $categoryPerformance = $this->productPerformanceRepository->getCategoryPerformance();

        return response()->json([
            'success' => true,
            'data' => [
                'top_selling_products' => $topSellingProducts,
                'products_with_low_stock' => $productsWithLowStock,
                'category_performance' => $categoryPerformance,
            ],
            'message' => __('messages.product_performance_retrieved_successfully'),
        ]);
    }
}
