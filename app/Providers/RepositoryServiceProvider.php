<?php

// app/Providers/RepositoryServiceProvider.php

namespace App\Providers;

use App\Repositories\AuthorRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\CategoryRepository;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\PublisherRepositoryInterface;
use App\Repositories\PublisherRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(AuthorRepositoryInterface::class, AuthorRepository::class);
        $this->app->bind(PublisherRepositoryInterface::class, PublisherRepository::class);
    }
}
