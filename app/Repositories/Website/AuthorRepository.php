<?php

namespace App\Repositories\Website;

use App\Models\Author;
use App\Repositories\Interfaces\Website\AuthorRepositoryInterface;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function getAllAuthors()
    {
        return Author::all();
    }
}
