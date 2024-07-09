<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Author;
use App\Models\Category;
use App\Models\Language;
use App\Models\Publisher;
use App\Models\Book;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
            'publisher_id' => Publisher::factory(),
            'publication_date' => $this->faker->date(),
            'language_id' => Language::factory(),
            'isbn10' => $this->faker->numerify('##########'),
            'isbn13' => $this->faker->numerify('#############'),
            'num_pages' => $this->faker->numberBetween(50, 1000),
            'dimensions' => $this->faker->numerify('##x##x## cm'),
            'weight' => $this->faker->randomFloat(2, 0.1, 5),
            'format' => $this->faker->randomElement(['PDF', 'Hard Copy', 'Audiobook']),
            'price' => $this->faker->randomFloat(2, 5, 100),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'description' => $this->faker->paragraph(),
            'img' => $this->faker->imageUrl(300, 400, 'books'),
        ];
    }
}
