<?php

namespace App\Repositories\Admin;

use App\Models\Publisher;
use App\Repositories\Interfaces\Admin\PublisherRepositoryInterface;

class PublisherRepository implements PublisherRepositoryInterface
{
    /**
     * Get all Publishers.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Publisher::paginate();
    }

    /**
     * Create a new Publisher.
     *
     * @param array $data
     * @return Publisher
     */
    public function create(array $data)
    {
        return Publisher::create($data);
    }

    /**
     * Update a Publisher by id.
     *
     * @param int $id
     * @param array $data
     * @return Publisher
     */
    public function update($id, array $data)
    {
        $publisher = $this->findById($id);
        $publisher->update($data);
        return $publisher;
    }

    /**
     * Delete a Publisher by id.
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $publisher = $this->findById($id);
        $publisher->delete();
    }

    /**
     * Find a Publisher by id.
     *
     * @param int $id
     * @return Publisher
     */
    public function findById($id)
    {
        return Publisher::findOrFail($id);
    }
}
