<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Concerns;

trait Environment
{
	/**
     * Load the database migrations.
     *
     * @return void
     */
    protected function loadMigrations()
    {
        $this->artisan('migrate:fresh');
    }

    /**
     * Load the database factories.
     *
     * @return void
     */
    protected function loadFactories()
    {
        $this->withFactories(__DIR__ . '/../../database/factories');
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', \Antvel\Users\Models\User::class);
        $app['config']->set('app.key', $this->generateRandomKey($app));
        $app['path.lang'] = $this->getFixturesDirectory('lang');
        $app['path.storage'] = __DIR__ . "/../../storage";
    }

    /**
     * Generate a random key for the application.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return string
     */
    protected function generateRandomKey($app)
    {
        return 'base64:'.base64_encode(random_bytes(
            $app['config']['app']['cipher'] == 'AES-128-CBC' ? 16 : 32
        ));
    }

    /**
     * Load the translations files.
     *
     * @param  string $path
     *
     * @return string
     */
    public function getFixturesDirectory(string $path): string
    {
        return __DIR__ . "/../../resources/{$path}";
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

    /**
     * Swaps the storage folder path.
     *
     * @return void
     */
    public function withStorageFolder()
    {
        $this->app->make('config')->set(
            'filesystems.disks.local.root',
            __DIR__ . '/../../storage/framework/testing/disks'
        );
    }

    /**
     * Set the test to be run using MySQL.
     *
     * @return void
     */
    protected function usingMySql()
    {
        $this->app->make('config')->set('database.default', self::TESTING_DB);
        $this->app->make('config')->set('database.connections.' . self::TESTING_DB, [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => '3306',
            'database' => self::TESTING_DB,
            'username' => 'root',
            'password' => getenv('TRAVIS') ? '' : 'secret',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        $this->loadMigrations();
    }
}
