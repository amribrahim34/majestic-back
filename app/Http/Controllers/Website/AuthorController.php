<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Website\AuthorRepositoryInterface;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    private $authorRepository;

    public function __construct(AuthorRepositoryInterface $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function index()
    {
        return $this->authorRepository->getAllAuthors();
    }
}
