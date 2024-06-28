<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BulkOrder;
use App\Models\Order;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BulkOrder>
 */
class BulkOrderFactory extends Factory
{
    protected $model = BulkOrder::class;

    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'institution_name' => $this->faker->company(),
            'quantity' => $this->faker->numberBetween(10, 1000),
            'total_amount' => $this->faker->randomFloat(2, 100, 10000),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }


    /**
     * Indicate that the bulk order is for a specific order.
     */
    public function forOrder(Order $order)
    {
        return $this->state(function (array $attributes) use ($order) {
            return [
                'order_id' => $order->id,
            ];
        });
    }

    /**
     * Indicate that the bulk order is for a large quantity.
     */
    public function largeQuantity()
    {
        return $this->state(function (array $attributes) {
            $quantity = $this->faker->numberBetween(500, 2000);
            return [
                'quantity' => $quantity,
                'total_amount' => $quantity * $this->faker->randomFloat(2, 10, 50),
            ];
        });
    }

    /**
     * Indicate that the bulk order is for a small quantity.
     */
    public function smallQuantity()
    {
        return $this->state(function (array $attributes) {
            $quantity = $this->faker->numberBetween(10, 100);
            return [
                'quantity' => $quantity,
                'total_amount' => $quantity * $this->faker->randomFloat(2, 10, 50),
            ];
        });
    }
}
