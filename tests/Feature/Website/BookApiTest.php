<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Language;

class BookApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // Run database seeders if you have any
    }

    public function testIndex()
    {
        Book::factory()->count(15)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'category',
                        'publisher',
                        'language',
                        // Add other fields you expect in the response
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function testShow()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $book->id,
                'title' => $book->title,
                // Assert other fields
            ]);
    }

    public function testByCategory()
    {
        $category = Category::factory()->create();
        Book::factory()->count(5)->create(['category_id' => $category->id]);

        $response = $this->getJson("/api/books/category/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'category' => ['id', 'name'],
                        // Other fields
                    ]
                ]
            ]);
    }

    public function testSearch()
    {
        $searchTerm = $this->faker->word;
        Book::factory()->create(['title' => "Test {$searchTerm} Book"]);
        Book::factory()->count(3)->create();

        $response = $this->getJson("/api/books/search?q={$searchTerm}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => "Test {$searchTerm} Book"]);
    }

    public function testLatest()
    {
        Book::factory()->count(15)->create();

        $response = $this->getJson('/api/books/latest?limit=5');

        $response->assertStatus(200)
            ->assertJsonCount(5)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'title',
                    'publication_date',
                    // Other fields
                ]
            ]);
    }

    public function testBestSellers()
    {
        Book::factory()->count(15)->create();

        $response = $this->getJson('/api/books/best-sellers?limit=5');

        $response->assertStatus(200)
            ->assertJsonCount(5)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'title',
                    'stock_quantity',
                    // Other fields
                ]
            ]);
    }
}
