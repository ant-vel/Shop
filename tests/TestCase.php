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

use Antvel\Antvel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use Environment;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadFactories();
        $this->loadMigrations();
    }

    /**
     * Swaps the storage folder path.
     *
     * @return void
     */
    public function withStorageFolder()
    {
        $storage = __DIR__ . '/../storage/framework/testing/disks';

        $this->app->make('config')->set(
            'filesystems.disks.local.root',
            $storage
        );
    }

    /**
     * Creates a fake file.
     *
     * @param  string $disk
     * @param  string $file
     *
     * @return UploadedFile
     */
    public function uploadFile($disk = 'avatars', $file = 'antvel.jpg')
    {
        $this->withStorageFolder();

        Storage::fake($disk);

        return UploadedFile::fake()->image('avatar.jpg');
    }
}
