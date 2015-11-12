<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        // Bind DbDropdownRepository for DropdownRepositoryInterface
        $this->app->bind(
            'App\Repos\Dropdowns\DropdownRepositoryInterface',
            'App\Repos\Dropdowns\DbDropdownRepository'
        );

        // Bind DbFileRepository for FileRepositoryInterface
        $this->app->bind(
            'App\Repos\Files\FileRepositoryInterface',
            'App\Repos\Files\DbFileRepository'
        );

    }
}
