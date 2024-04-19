<?php

namespace App\Providers;

use App\Contracts\Admin\IAuthService;
use App\Services\Admin\AdminAuthService;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(IAuthService::class, AdminAuthService::class);
    }
}
