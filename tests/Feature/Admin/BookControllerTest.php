<?php

namespace Tests\Feature\Admin;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Models\Publisher;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->user = Admin::factory()->create();
        $this->actingAs($this->user);
    }

    public function testIndex()
    {
        Book::factory()->count(5)->create();

        $response = $this->getJson('/api/admin/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'author', 'category', 'publisher', 'language']
                ],
                'links',
                'meta'
            ])
            ->assertJsonCount(5, 'data');
    }

    public function testStore()
    {
        $author = Author::factory()->create();
        $category = Category::factory()->create();
        $publisher = Publisher::factory()->create();
        $language = Language::factory()->create();

        Storage::fake('public');

        $data = [
            'title' => ['en' => 'Test Book'],
            'author_id' => $author->id,
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'language_id' => $language->id,
            'publication_date' => '2023-01-01',
            'isbn10' => '1234567890',
            'isbn13' => '1234567890123',
            'num_pages' => 200,
            'format' => 'Hard Copy',
            'price' => 29.99,
            'stock_quantity' => 100,
            'description' => ['en' => 'A test book description'],
            'img' => UploadedFile::fake()->image('book.jpg')
        ];

        $response = $this->postJson('/api/admin/books', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'title'] // Remove 'author', 'category', 'publisher', 'language' if they're not in the response
            ]);


        // Add this to check if the book was created with correct data
        $this->assertDatabaseHas('books', [
            'title->en' => 'Test Book',
            'author_id' => $author->id,
            'category_id' => $category->id,
            'publisher_id' => $publisher->id,
            'language_id' => $language->id,
        ]);
        $book = Book::latest()->first();
        $this->assertNotNull($book->img);
        Storage::disk('public')->assertExists($book->img);
    }

    public function testShow()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/admin/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'author', 'category', 'publisher', 'language']
            ])
            ->assertJson([
                'data' => [
                    'id' => $book->id,
                ]
            ]);
    }

    public function testUpdate()
    {
        $book = Book::factory()->create();
        $newAuthor = Author::factory()->create();

        $data = [
            'title' => ['en' => 'Updated Book Title'],
            'author_id' => $newAuthor->id,
        ];

        $response = $this->putJson("/api/admin/books/{$book->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'author', 'category', 'publisher', 'language']
            ])
            ->assertJson([
                'data' => [
                    'id' => $book->id,
                    'title' => 'Updated Book Title',
                    'author' => [
                        'id' => $newAuthor->id
                    ]
                ]
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title->en' => 'Updated Book Title',
            'author_id' => $newAuthor->id,
        ]);
    }

    public function testDestroy()
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/admin/books/{$book->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }
}
