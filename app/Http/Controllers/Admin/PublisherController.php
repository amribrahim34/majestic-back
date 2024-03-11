<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublisherRequest;
use App\Http\Requests\UpdatePublisherRequest;
use App\Http\Resources\PublisherResource;
use App\Models\Publisher;
use App\Repositories\Interfaces\PublisherRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PublisherController extends Controller
{

    protected $publisherRepository;

    public function __construct(PublisherRepositoryInterface $publisherRepository)
    {
        $this->publisherRepository = $publisherRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $publishers = $this->publisherRepository->all();
        return response()->json([
            'data' => PublisherResource::collection($publishers)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePublisherRequest $request)
    {
        $publisher = $this->publisherRepository->create($request->validated());
        return response()->json([
            'message' => __('publishers.created'),
            'data' => new PublisherResource($publisher)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Publisher $publisher)
    {
        return new PublisherResource($publisher);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePublisherRequest $request, Publisher $publisher)
    {
        $publisher = $this->publisherRepository->update($publisher->id, $request->validated());
        return response()->json([
            'message' => __('publishers.updated'),
            'data' => new PublisherResource($publisher)
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publisher $publisher)
    {
        $this->publisherRepository->delete($publisher->id);
        return response()->json(['message' => __('publishers.deleted')], Response::HTTP_NO_CONTENT);
    }
}
