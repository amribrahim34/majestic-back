<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Http\Resources\Admin\AuthorResource;
use App\Models\Author;
use App\Repositories\Interfaces\Admin\AuthorRepositoryInterface;
use Illuminate\Http\Response;
use App\Exports\AuthorExport;
use App\Imports\AuthorImport;
use App\Jobs\ProcessAuthorUpdate;
use App\Jobs\ProcessImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;


class AuthorController extends Controller
{

    protected $authors;

    public function __construct(AuthorRepositoryInterface $authors)
    {
        $this->authors = $authors;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authors = $this->authors->all();
        return AuthorResource::collection($authors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthorRequest $request)
    {
        $author = $this->authors->create($request->validated());
        return response()->json([
            'data' => new AuthorResource($author),
            'message' => __('authors.created')
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        // $author = $this->authors->findById($id);
        return new AuthorResource($author);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthorRequest $request, Author $author)
    {
        $author = $this->authors->update($author->id, $request->validated());
        return response()->json([
            'data' => new AuthorResource($author),
            'message' => __('authors.updated')
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        $this->authors->delete($author->id);
        return response()->json(['message' => __('authors.deleted')], Response::HTTP_NO_CONTENT);
    }


    /**
     * Export authors to Excel.
     * @OA\Get(
     *     path="/api/admin/authors/export",
     *     summary="Export authors to Excel",
     *     tags={"Authors"},
     *     @OA\Response(response="200", description="Excel file with authors data")
     * )
     */
    public function export()
    {
        return Excel::download(new AuthorExport, 'authors.xlsx');
    }

    /**
     * Import authors from Excel.
     * @OA\Post(
     *     path="/api/admin/authors/import",
     *     summary="Import authors from Excel",
     *     tags={"Authors"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="file",
     *                     format="file"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="202", description="Import job started")
     * )
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        $file = $request->file('file');
        $filePath = $file->store('imports');

        $job = new ProcessImport(AuthorImport::class, $filePath);
        $this->dispatch($job);

        return response()->json([
            'message' => 'Import started',
            'job_id' => $job->getJobId()
        ], 202);
    }

    /**
     * Update authors from CSV file.
     *
     * @param UpdateAuthorCSVRequest $request
     * @return Response
     */
    public function updateFromCSV(Request $request)
    {
        $file = $request->file('csv_file');
        $path = $file->store('temp');

        $job = new ProcessAuthorUpdate($path);
        Bus::dispatch($job);

        return response()->json([
            'message' => 'Author update process started',
            'job_id' => $job->getJobId()
        ], 202);
    }

    /**
     * Get import progress.
     * @OA\Get(
     *     path="/api/admin/authors/import/progress/{jobId}",
     *     summary="Get import progress",
     *     tags={"Authors"},
     *     @OA\Parameter(
     *         name="jobId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Import progress")
     * )
     */
    public function importProgress($jobId)
    {
        $progress = ProcessImport::getProgress($jobId);
        return response()->json(['progress' => $progress]);
    }
}
