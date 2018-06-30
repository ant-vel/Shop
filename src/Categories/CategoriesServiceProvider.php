<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Categories;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class CategoriesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            View::share('categories_menu', $this->categoriesMenu());
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Returns the categories menu.
     *
     * @return array
     */
    protected function categoriesMenu() : array
    {
        if (! $this->app->bound($repository = 'category.repository.cahe')) {
            return [];
        }

        return $this->app->make($repository)->categoriesWithProducts()->mapWithKeys(function ($item) {
            return [$item->id => $item];
        })->all();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['antvel-categories'];
    }
}
