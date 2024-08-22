<?php



namespace App\Repositories\Admin\Dashboard;

use App\Models\Book;
use App\Models\OrderItem;
use App\Models\Category;
use App\Repositories\Interfaces\Admin\Dashboard\ProductPerformanceRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductPerformanceRepository implements ProductPerformanceRepositoryInterface
{
    public function getTopSellingProducts($limit = 10)
    {
        return OrderItem::select('book_id', 'title', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('book_id', 'title')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }

    public function getProductsWithLowStock($threshold = 10)
    {
        return Book::where('stock_quantity', '<=', $threshold)
            ->select('id', 'title', 'stock_quantity')
            ->orderBy('stock_quantity')
            ->get();
    }

    public function getCategoryPerformance()
    {
        return Category::withCount(['books' => function ($query) {
            $query->whereHas('orderItems');
        }])
            ->withSum(['books' => function ($query) {
                $query->whereHas('orderItems');
            }], 'price')
            ->orderByDesc('books_count')
            ->get();
    }
}
