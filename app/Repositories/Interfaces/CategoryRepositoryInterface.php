<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface CategoryRepositoryInterface
{
    public function all();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function bulkDelete($ids);
    public function findByLanguage($languageCode); // For language-based retrieval
}
