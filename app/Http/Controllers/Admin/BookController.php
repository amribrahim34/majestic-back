<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\Admin\BookResource;
use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Imports\BooksImport;
use Maatwebsite\Excel\Facades\Excel;

class BookController extends Controller
{
    protected $bookRepository;

    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = $this->bookRepository->all();
        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = $this->bookRepository->create($request->validated());
        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $book = $this->bookRepository->findById($id);
        return new BookResource($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, $id)
    {
        $book = $this->bookRepository->update($id, $request->validated());
        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->bookRepository->delete($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
        Excel::import(new BooksImport, $request->file('file'));
        return response()->json(['msg' => "success"], Response::HTTP_CREATED);
    }

    public function downloadTemplate()
    {
        return response()->download(storage_path('app/templates/book_import_template.xlsx'));
    }
}
