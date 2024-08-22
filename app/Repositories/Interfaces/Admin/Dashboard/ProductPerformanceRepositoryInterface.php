<?php


namespace App\Repositories\Interfaces\Admin\Dashboard;

interface ProductPerformanceRepositoryInterface
{
    public function getTopSellingProducts($limit = 10);
    public function getProductsWithLowStock($threshold = 10);
    public function getCategoryPerformance();
}
