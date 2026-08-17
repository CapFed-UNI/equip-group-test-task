<?php

namespace App\Providers;

use App\Services\GroupTreeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GroupTreeService::class);
    }

    public function boot(): void
    {
        //
    }
}
