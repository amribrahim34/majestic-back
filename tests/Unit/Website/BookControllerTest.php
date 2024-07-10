<?php

namespace Tests\Unit\Website;

use Tests\TestCase;
use App\Models\Book;
use App\Models\Category;
use App\Http\Controllers\Website\BookController;
use App\Repositories\Interfaces\Website\BookRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $bookRepository;
    protected $bookController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookRepository = Mockery::mock(BookRepositoryInterface::class);
        $this->bookController = new BookController($this->bookRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testIndex()
    {
        $books = Book::factory()->count(5)->make();
        $this->bookRepository->shouldReceive('getAllBooks')->once()->andReturn($books);

        $response = $this->bookController->index();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($books->toJson(), $response->getContent());
    }

    public function testShow()
    {
        $book = Book::factory()->make();
        $this->bookRepository->shouldReceive('getBookById')->with(1)->once()->andReturn($book);

        $response = $this->bookController->show(1);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($book->toJson(), $response->getContent());
    }

    public function testByCategory()
    {
        $books = Book::factory()->count(3)->make();
        $this->bookRepository->shouldReceive('getBooksByCategory')->with(1)->once()->andReturn($books);

        $response = $this->bookController->byCategory(1);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($books->toJson(), $response->getContent());
    }

    public function testSearch()
    {
        $books = Book::factory()->count(2)->make();
        $request = new Request(['q' => 'test']);
        $this->bookRepository->shouldReceive('searchBooks')->with('test')->once()->andReturn($books);

        $response = $this->bookController->search($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($books->toJson(), $response->getContent());
    }

    public function testLatest()
    {
        $books = Book::factory()->count(10)->make();
        $request = new Request(['limit' => 10]);
        $this->bookRepository->shouldReceive('getLatestBooks')->with(10)->once()->andReturn($books);

        $response = $this->bookController->latest($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($books->toJson(), $response->getContent());
    }

    public function testBestSellers()
    {
        $books = Book::factory()->count(5)->make();
        $request = new Request(['limit' => 5]);
        $this->bookRepository->shouldReceive('getBestSellingBooks')->with(5)->once()->andReturn($books);

        $response = $this->bookController->bestSellers($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString($books->toJson(), $response->getContent());
    }
}
