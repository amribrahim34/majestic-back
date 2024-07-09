<?php

namespace Tests\Unit\Controllers\Admin;

use App\Http\Controllers\Admin\BookController;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\Admin\BookResource;
use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
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
        $books = collect([new Book(), new Book()]);
        $this->bookRepository->shouldReceive('all')->once()->andReturn($books);

        $response = $this->bookController->index();

        $this->assertInstanceOf(BookResource::class, $response->first());
        $this->assertEquals(2, $response->count());
    }

    public function testStore()
    {
        $validatedData = ['title' =>  'Test Book', 'author_id' => 1];
        $book = new Book($validatedData);

        $request = Mockery::mock(StoreBookRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($validatedData);

        $this->bookRepository->shouldReceive('create')->once()->with($validatedData)->andReturn($book);

        $response = $this->bookController->store($request);

        $this->assertInstanceOf(BookResource::class, $response);
        $this->assertEquals($book->title, $response->resource->title);
    }

    public function testShow()
    {
        $book = new Book(['id' => 1, 'title' => 'Test Book']);

        $this->bookRepository->shouldReceive('findById')->once()->with(1)->andReturn($book);

        $response = $this->bookController->show(1);

        $this->assertInstanceOf(BookResource::class, $response);
        $this->assertEquals($book->id, $response->resource->id);
    }

    public function testUpdate()
    {
        $id = 1;
        $validatedData = ['title' => 'Updated Book'];
        $updatedBook = new Book(['id' => $id] + $validatedData);

        $request = Mockery::mock(UpdateBookRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($validatedData);

        $this->bookRepository->shouldReceive('update')->once()->with($id, $validatedData)->andReturn($updatedBook);

        $response = $this->bookController->update($request, $id);

        $this->assertInstanceOf(BookResource::class, $response);
        $this->assertEquals($updatedBook->title, $response->resource->title);
    }

    public function testDestroy()
    {
        $id = 1;

        $this->bookRepository->shouldReceive('delete')->once()->with($id);

        $response = $this->bookController->destroy($id);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
