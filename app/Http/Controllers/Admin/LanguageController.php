<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Http\Resources\LanguageResource;
use App\Models\Language;
use App\Repositories\Interfaces\Admin\LanguageRepositoryInterface;

class LanguageController extends Controller
{
    protected $languageRepository;

    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = $this->languageRepository->all();
        return response()->json([
            'data' => LanguageResource::collection($languages)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language)
    {
        return new LanguageResource($language);
    }
}
