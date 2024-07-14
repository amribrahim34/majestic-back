<?php

namespace App\Repositories\Website;

use App\Models\Publisher;
use App\Repositories\Interfaces\Website\PublisherRepositoryInterface;

class PublisherRepository implements PublisherRepositoryInterface
{
    public function getAllPublishers()
    {
        return Publisher::all();
    }
}
