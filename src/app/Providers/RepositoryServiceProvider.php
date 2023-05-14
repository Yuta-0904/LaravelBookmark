<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// repository
use App\Repositories\BookmarkRepository;
use App\Repositories\BookmarkCategoryRepository;

// interface
use App\Interfaces\BookMarkInterface;
use App\Interfaces\BookmarkCategoryInterface;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BookMarkInterface::class, BookmarkRepository::class);
        $this->app->bind(BookmarkCategoryInterface::class, BookmarkCategoryRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
