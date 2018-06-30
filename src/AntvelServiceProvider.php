<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AntvelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerResources();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServices();
        $this->registerProviders();
        $this->registerServicesAliases();
    }

    /**
     * Register the Antvel resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'antvel');
    }

    /**
     * Register the Antvel routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::namespace('Antvel')->middleware('web')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * Register Antvel services in the container.
     *
     * @return void
     */
    protected function registerServices()
    {
        foreach (Antvel::bindings() as $key => $value) {
            is_numeric($key)
                ? $this->app->singleton($value)
                : $this->app->singleton($key, $value);
        }
    }

    /**
     * Register Antvel services aliases in the container.
     *
     * @return void
     */
    protected function registerServicesAliases()
    {
        foreach (Antvel::alias() as $key => $value) {
            $this->app->alias($value, $key);
        }
    }

    /**
     * Register Antvel services providers.
     *
     * @return void
     */
    protected function registerProviders()
    {
        foreach (Antvel::providers() as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Antvel::class];
    }
}
