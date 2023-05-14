<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// repository
use App\Repositories\BookmarkRepository;
use App\Repositories\BookmarkCategoryRepository;
use App\Repositories\UserRepository;

// interface
use App\Interfaces\BookMarkInterface;
use App\Interfaces\BookmarkCategoryInterface;
use App\Interfaces\UserInterface;

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
        $this->app->bind(UserInterface::class, UserRepository::class);
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
