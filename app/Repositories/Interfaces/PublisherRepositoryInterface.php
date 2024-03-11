<?php

namespace App\Repositories\Interfaces;

interface PublisherRepositoryInterface
{
    public function all();
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findById($id);
}
