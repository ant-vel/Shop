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

use Illuminate\Support\ServiceProvider;

class AntvelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'antvel');

        if ($this->app->runningInConsole()) {
            $this->publishResources();
        }
    }

    /**
     * Publish the antvel resources files.
     *
     * @return void
     */
    protected function publishResources()
    {
        $this->publishes([
            __DIR__ . '/../config/' => config_path()
        ], 'antvel-config');

         $this->publishes([
            __DIR__ . '/../database/seeds' => database_path('seeds')
        ], 'antvel-seeds');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/antvel'),
        ], 'antvel-trans');
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
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Antvel::class];
    }
}
