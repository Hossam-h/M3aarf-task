<?php

namespace App\Providers;

use App\Repositories\Interfaces\PlaylistRepositoryInterface;
use App\Repositories\PlaylistRepository;
use Illuminate\Support\ServiceProvider;

/**
 * RepositoryServiceProvider
 *
 * Binds repository interfaces to their concrete implementations.
 * This enables dependency injection and makes testing easier.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * Bind all repository interfaces to their implementations.
     */
    public function register(): void
    {
        $this->app->bind(
            PlaylistRepositoryInterface::class,
            PlaylistRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
