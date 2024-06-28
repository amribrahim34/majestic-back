<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\BookRequest;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookRequest>
 */
class BookRequestFactory extends Factory
{
    protected $model = BookRequest::class;

    public function definition()
    {
        return [
            'book_title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'user_id' => User::factory(),
            'additional_info' => $this->faker->optional(0.7)->paragraph(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the book request has additional information.
     */
    public function withAdditionalInfo()
    {
        return $this->state(function (array $attributes) {
            return [
                'additional_info' => $this->faker->paragraph(),
            ];
        });
    }

    /**
     * Indicate that the book request is made by a specific user.
     */
    public function byUser(User $user)
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }
}
