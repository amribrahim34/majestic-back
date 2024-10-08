<?php

namespace App\Repositories\Interfaces\Admin;

use Illuminate\Http\Request;

interface PostRepositoryInterface
{
    public function paginated();
    public function findById($id);
    public function create($post);
    public function edit($post, $data);
    public function delete($post);
    public function bulkDelete($ids);
}
