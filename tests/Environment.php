<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests;

use Illuminate\Support\Facades\Artisan;

trait Environment
{
	/**
     * Load the database migrations.
     *
     * @return void
     */
    protected function loadMigrations()
    {
        Artisan::call('migrate:refresh');

        $this->artisan('migrate', [
            '--realpath' => __DIR__ . '/../database/migrations'
        ]);
    }

    /**
     * Load the database factories.
     *
     * @return void
     */
    protected function loadFactories()
    {
        $this->withFactories(__DIR__ . '/../database/factories');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['path.lang'] = $this->getFixturesDirectory('lang');
    }

    /**
     * Load the translations files.
     *
     * @param  string $path
     * @return string
     */
    public function getFixturesDirectory(string $path): string
    {
        return __DIR__ . "/../resources/{$path}";
    }

    /**
     * Get package service providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Antvel\AntvelServiceProvider::class
        ];
    }
}
