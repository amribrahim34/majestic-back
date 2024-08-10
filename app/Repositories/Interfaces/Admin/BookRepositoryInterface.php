<?php

namespace App\Repositories\Interfaces\Admin;

interface BookRepositoryInterface
{
    public function all();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function importImagesFromCsv($filePath);
}
