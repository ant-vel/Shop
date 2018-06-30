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

use Illuminate\Http\UploadedFile;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

trait InteractWithPictures
{
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

        return UploadedFile::fake()->image($file);
    }

    /**
     * Creates and persists fake files.
     *
     * @param  string $disk
     * @param  string $file
     *
     * @return UploadedFile
     */
    public function persistentUpload($disk = 'avatars', $file = 'antvel.jpg')
    {
        $this->withStorageFolder();

        Storage::persistentFake($disk);

        return UploadedFile::fake()->image($file);
    }

    /**
     * Clean the given directory.
     *
     * @param  string $disk
     *
     * @return void
     */
    public function cleanDirectory($disk)
    {
        (new Filesystem)->cleanDirectory(
            storage_path('framework/testing/disks/' . $disk)
        );
    }


    /**
     * Returns a uploaded file name.
     *
     * @param  string $fileName
     * @return string
     */
    protected function image($fileName)
    {
        $fileName = explode('/', $fileName);

        return end($fileName);
    }
}
