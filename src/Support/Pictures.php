<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Pictures
{
	/**
	 * The upload process information.
	 *
	 * @var Collection
	 */
	protected $data = null;

	/**
	 * The allowed fields.
	 *
	 * @var array
	 */
	protected $allowed = ['_pictures_file', '_pictures_current', '_pictures_delete'];

	/**
	 * Creates a new instance with the given attributes.
	 *
	 * @param  array $attributes
	 *
	 * @return slef
	 */
	public static function prepare(array $attributes)
	{
		$pictures = new static;

		$pictures->data = Collection::make($attributes)->only($pictures->allowed);

		return $pictures;
	}

	/**
	 * Checks whether the request asked for a file updating.
	 *
	 * @return bool
	 */
	public function wasUpdated() : bool
	{
		return $this->data->has('_pictures_delete') || $this->data->has('_pictures_file');
	}

	/**
	 * Stores the file in the given directory.
	 *
	 * @param  string $directory
	 *
	 * @return null|string
	 */
	public function store($directory)
	{
		//We clean the given directory to avoid file duplication.
		$this->purge($directory);

		//We verify whether there was a deletion request. If so, the file path will be null.
		if ($this->data->has('_pictures_delete')) {
			return null;
		}

		//We upload and store the file in the given directory and retrieve its path.
		return $this->upload($directory);
	}

	/**
	 * Uploads a file in the given directory.
	 *
	 * @param  string $directory
	 *
	 * @return string
	 */
	protected function upload($directory) : string
	{
		return $this->data->get('_pictures_file')->store($directory);
	}

	/**
	 * Cleans the given directory.
	 *
	 * @param  string $directory
	 *
	 * @return void
	 */
	protected function purge($directory)
	{
		$files = Collection::make(Storage::files($directory));

		$files->filter(function($value) {
			return Str::contains($value, $this->startWith());
		})->tap(function($collection) {
			Storage::delete($collection->all());
		});
	}

	/**
	 * Returns the file name save in the database.
	 *
	 * @return string
	 */
	protected function startWith() : string
	{
		//The needle is the file name to be deleted from the location of the file.
		$needle = $this->data->get('_pictures_current');

		$current = explode('/', $needle);

		//We get the filename from the given needle.
		$fileName = Arr::last($current);

		return Arr::first(explode('.', $fileName));
	}

	/**
	 * Returns the process information.
	 *
	 * @return Collection
	 */
	public function data() : Collection
	{
		return $this->data;
	}
}
