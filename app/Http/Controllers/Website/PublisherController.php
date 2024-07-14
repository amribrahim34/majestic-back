<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Website\PublisherRepositoryInterface;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    private $publisherRepository;

    public function __construct(PublisherRepositoryInterface $publisherRepository)
    {
        $this->publisherRepository = $publisherRepository;
    }

    public function index()
    {
        return $this->publisherRepository->getAllPublishers();
    }
}
