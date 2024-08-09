<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    protected $model;

    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    public function all()
    {
        return $this->model->with(['user', 'items'])->paginate();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $order = $this->find($id);
        return $order->update($data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findWithItems($id)
    {
        return $this->model->with('items')->findOrFail($id);
    }

    public function getByStatus($status)
    {
        return $this->model->where('status', $status)->get();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->model->whereBetween('order_date', [$startDate, $endDate])->get();
    }

    public function updateStatus($id, $status)
    {
        $order = $this->find($id);
        $order->status = $status;
        $order->save();
        return $order;
    }

    public function getOrdersByUser($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function getTotalRevenue($startDate = null, $endDate = null)
    {
        $query = $this->model->select(DB::raw('SUM(total_amount) as total_revenue'));

        if ($startDate && $endDate) {
            $query->whereBetween('order_date', [$startDate, $endDate]);
        }

        return $query->first()->total_revenue;
    }

    public function getMostValuableCustomers($limit = 10)
    {
        return $this->model->select('user_id', DB::raw('SUM(total_amount) as total_spent'))
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->with('user')
            ->get();
    }
}
