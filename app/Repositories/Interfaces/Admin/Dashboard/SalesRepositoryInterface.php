<?php

namespace App\Repositories\Interfaces\Admin\Dashboard;

interface SalesRepositoryInterface
{
    public function getTotalRevenue();
    public function getNumberOfOrders();
    public function getAverageOrderValue();
    public function getSalesTrend($period);
}
