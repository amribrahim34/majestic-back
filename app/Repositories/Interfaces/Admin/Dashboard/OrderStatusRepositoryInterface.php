<?php

namespace App\Repositories\Interfaces\Admin\Dashboard;

interface OrderStatusRepositoryInterface
{
    public function getRecentOrders($limit = 10);
    public function getOrdersByStatus();
}
