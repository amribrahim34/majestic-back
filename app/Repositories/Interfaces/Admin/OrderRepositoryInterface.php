<?php

namespace App\Repositories\Interfaces\Admin;

interface OrderRepositoryInterface
{
    public function findWithItems($id);
    public function getByStatus($status);
    public function getByDateRange($startDate, $endDate);
    public function updateStatus($id, $status);
    public function getOrdersByUser($userId);
    public function getTotalRevenue($startDate = null, $endDate = null);
    public function getMostValuableCustomers($limit = 10);
}
