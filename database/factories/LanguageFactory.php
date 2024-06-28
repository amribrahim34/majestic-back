<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Language;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Language>
 */
class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition()
    {
        return [
            'language_name' => $this->faker->unique()->word(),
            'iso_code' => $this->faker->unique()->lexify('???'),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the language has an ISO code.
     */
    public function withIsoCode()
    {
        return $this->state(function (array $attributes) {
            return [
                'iso_code' => $this->faker->unique()->languageCode(),
            ];
        });
    }

    /**
     * Indicate that the language is English.
     */
    public function english()
    {
        return $this->state(function (array $attributes) {
            return [
                'language_name' => 'English',
                'iso_code' => 'en',
            ];
        });
    }

    /**
     * Indicate that the language is Arabic.
     */
    public function arabic()
    {
        return $this->state(function (array $attributes) {
            return [
                'language_name' => 'Arabic',
                'iso_code' => 'ar',
            ];
        });
    }
}
