<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Author;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'middle_name' => $this->faker->boolean(70) ? $this->faker->firstName() : null,
            'biography' => $this->faker->paragraphs(3, true),
            'birth_date' => $this->faker->dateTimeBetween('-100 years', '-18 years')->format('Y-m-d'),
            'death_date' => function (array $attributes) {
                return $this->faker->optional(0.3)->dateTimeBetween($attributes['birth_date'], 'now')?->format('Y-m-d');
            },            'country' => $this->faker->country(),
        ];
    }

    /**
     * Indicate that the author is deceased.
     */
    public function deceased()
    {
        return $this->state(function (array $attributes) {
            return [
                'death_date' => $this->faker->dateTimeBetween($attributes['birth_date'], 'now')->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indicate that the author is living.
     */
    public function living()
    {
        return $this->state(function (array $attributes) {
            return [
                'death_date' => null,
            ];
        });
    }
}
