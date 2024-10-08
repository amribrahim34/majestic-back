<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\Admin\BookResource;
use App\Repositories\Interfaces\Admin\BookRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Imports\BooksImport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;



use App\Exports\BookExport;
use App\Imports\BookUpdate;;

use App\Jobs\ProcessImport;
use Illuminate\Support\Facades\Bus;

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
        Log::alert('tihis is the request', [$request->file]);
        $path = $request->file('file')->store('imports');
        // $job = new ProcessImport(BookUpdate::class, $path);
        // Bus::dispatch($job);
        // $this->dispatch($job);
        Log::alert('tihis is the path', [$path]);

        Excel::import(new BooksImport, $path);

        // Excel::import(new BooksImport, $request->file('file'));
        return response()->json(['msg' => "success"], Response::HTTP_CREATED);
    }

    public function downloadTemplate()
    {
        return response()->download(storage_path('app/templates/book_import_template.xlsx'));
    }

    public function importImages(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->storeAs('imports', 'books_images_' . time() . '.csv');

        try {
            $affected = $this->bookRepository->importImagesFromCsv(storage_path('app/' . $path));
            Log::info("Book image import completed successfully. Updated $affected books.");
            return response()->json([
                'message' => "Import completed successfully. Updated $affected books.",
                'affected' => $affected
            ], 200);
        } catch (\Exception $e) {
            Log::error('Book image import failed', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        } finally {
            // Optionally remove the uploaded file
            // \Storage::delete($path);
        }
    }

    public function export()
    {
        return Excel::download(new BookExport, 'books.xlsx');
    }

    public function importUpdate(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $path = $request->file('file')->store('imports');
        // $job = new ProcessImport(BookUpdate::class, $path);
        // Bus::dispatch($job);
        // $this->dispatch($job);

        Excel::import(new BookUpdate, $path);

        return response()->json(['message' => 'Import started']);

        // return response()->json(['message' => 'Import job queued successfully', 'job_id' => $job->getJobId()]);
    }
}
