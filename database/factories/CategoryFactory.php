<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'category_name' => $this->faker->unique()->words(2, true),
            'parent_id' => null,
            'description' =>  $this->faker->optional()->paragraph(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the category is a child category.
     */
    public function child()
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => Category::factory(),
            ];
        });
    }

    /**
     * Indicate that the category has a specific parent.
     */
    public function childOf(Category $parent)
    {
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'parent_id' => $parent->id,
            ];
        });
    }

    /**
     * Indicate that the category has a description.
     */
    public function withDescription()
    {
        return $this->state(function (array $attributes) {
            return [
                'description' => $this->faker->paragraph(),
            ];
        });
    }
}
