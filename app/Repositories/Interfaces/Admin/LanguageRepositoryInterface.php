<?php

namespace App\Repositories\Interfaces\Admin;

use Illuminate\Http\Request;

interface LanguageRepositoryInterface
{
    public function all();
    public function findById($id);
}
