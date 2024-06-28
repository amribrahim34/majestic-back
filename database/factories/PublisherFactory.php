<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Publisher;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Publisher>
 */
class PublisherFactory extends Factory
{
    protected $model = Publisher::class;

    public function definition()
    {
        return [
            'publisher_name' => [
                'en' => $this->faker->company(),
                'ar' => $this->faker->company(),
            ],
            'logo' => $this->faker->imageUrl(200, 200, 'business', true),
            'location' => $this->faker->optional()->city(),
            'website' => $this->faker->optional()->url(),
            'created_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the publisher has a location.
     */
    public function withLocation()
    {
        return $this->state(function (array $attributes) {
            return [
                'location' => $this->faker->city(),
            ];
        });
    }

    /**
     * Indicate that the publisher has a website.
     */
    public function withWebsite()
    {
        return $this->state(function (array $attributes) {
            return [
                'website' => $this->faker->url(),
            ];
        });
    }

    /**
     * Indicate that the publisher has both a location and a website.
     */
    public function withFullDetails()
    {
        return $this->state(function (array $attributes) {
            return [
                'location' => $this->faker->city(),
                'website' => $this->faker->url(),
            ];
        });
    }
}
