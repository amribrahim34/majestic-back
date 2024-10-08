<?php

namespace App\Repositories\Interfaces\Admin;

interface PublisherRepositoryInterface
{
    public function all();
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findById($id);
}
