<?php

namespace App\Repositories\Interfaces\Admin\Dashboard;

interface CustomerInsightsRepositoryInterface
{
    public function getNewVsReturningCustomers($period);
    public function getCustomerGeographicDistribution();
    public function getTopCustomersByRevenue($limit = 10);
}
