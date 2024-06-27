<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface LanguageRepositoryInterface
{
    public function all();
    public function findById($id);
}
