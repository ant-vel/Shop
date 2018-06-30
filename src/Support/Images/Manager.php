<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Support\Images;

use RuntimeException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Manager
{
	/**
	 * The maximum pictures allowed to be uploaded per products.
	 */
	const MAX_PICS = 5;

	/**
	 * The pictures directory.
	 *
	 * @var string
	 */
	protected $directory = null;

	/**
	 * The requested pictures information.
	 *
	 * @var Collection
	 */
	protected $pictures = null;

	/**
	 * Creates a new instance.
	 *
	 * @param  array $pictures
	 *
	 * @return self
	 */
	public static function parse($pictures)
	{
		$uploader = new static;

		$uploader->pictures = Collection::make($pictures);

		return $uploader;
	}

	/**
	 * Set the paths where to save files.
	 *
	 * @param  string $path
	 *
	 * @return self
	 */
	public function on($path)
	{
		$this->directory = $path;

		return $this;
	}

	protected function validate()
	{
		if (is_null($this->directory)) {
			throw new RuntimeException('You have to provide a valid directory!');
		}
	}

	/**
	 * Normalize the given bag.
	 *
	 * @param  array\null $bag
	 *
	 * @return Collection
	 */
	protected function normalizeBag($bag) : Collection
	{
		return Collection::make($bag)->filter(function ($item) {
			return is_string($item) ? trim($item) !== '' : $item;
		});
	}

	/**
	 * Returns the new files.
	 *
	 * @return Collection
	 */
	protected function files() : Collection
	{
		$storing = $this->pictures->get('storing');

		return $this->normalizeBag(
			is_array($storing) ? $storing : [$storing]
		);
	}

	/**
	 * Returns the new files.
	 *
	 * @return Collection
	 */
	protected function deleting() : Collection
	{
		return $this->normalizeBag(
			$this->pictures->get('deleting')
		);
	}

	/**
	 * Store the given pictures.
	 *
	 * @return array
	 */
	public function store() : array //check whether this method is required, otherwise, we have to rename to delte
	{
		$this->validate();

		if ($this->files()->isEmpty()) {
			return [];
		}

		return $this->files()->flatMap(function ($item) {
			$files[] = ['path' => $item->store($this->directory)];
			return $files;
		})->all();
	}

	/**
	 * Update the given pictures.
	 *
	 * @param  Collection $current
	 *
	 * @return string|array
	 */
	public function update($current)
	{
		$this->validate();

		if ($this->files()->isEmpty()) {
			return $this->currentPictures($current);
		}

		$result = $this->mapUpdatedPictures($current);

		if (count($result) == 1 && is_array($result[0])) {
			return $result[0];
		}

		return $result;
	}

	/**
	 * Map the current pictures.
	 *
	 * @param  array $current
	 *
	 * @return array
	 */
	protected function currentPictures($current)
	{
		if (is_string($current) || is_null($current) || count($current) == 0){
			return ['id' => null, 'path' => $current];
		}

		return $current->map(function ($item) {
			$array = [
				'id' => $item['id'],
				'path' => $item['path']
			];

			return $array;
		})->all();
	}

	/**
	 * Map the updated pictures.
	 *
	 * @param $current Collection
	 *
	 * @return string|array
	 */
	protected function mapUpdatedPictures($current)
	{
		$this->deleteCurrentFiles($current);

		return $this->files()->flatMap(function ($item, $key) {
			$files[] = [
				'id' => $key,
				'path' => $item->store($this->directory)
			];

			return $files;
		})->all();
	}

	/**
	 * Checks whether the request wants a deletion.
	 *
	 * @return bool
	 */
	public function wantsDeletion()
	{
		return ! is_null($this->deleting()) && $this->deleting()->count() > 0;
	}

	/**
	 * Deletes the given pictures.
	 *
	 * @param  Collection $current
	 *
	 * @return array|bool
	 */
	public function delete($current)
	{
		if (is_string($current)) {
			return $this->deleteCurrentFiles($current);
		}

		$toDelete = $this->deleting()->keys()->all();

		$this->deleteCurrentFiles(
			$current->whereIn('id', $toDelete)
		);

		return $toDelete;
	}

	/**
	 * Deletes the given pictures.
	 *
	 * @param  array\Collection $current
	 *
	 * @return void
	 */
	protected function deleteCurrentFiles($current)
	{
		$current = $current instanceof Collection
			? $current
			: Collection::make($current);

		$files = $current->pluck('path');

		//if the current value is string, it means that we are dealing with one
		//file instead with an array of them. So, we have to make sure to add
		//it to the final collection in order for it to be deleted.
		if (is_string($current->first())) {
			$files->push($current->first());
		}

		Storage::delete($files->all());

		$this->deleteRelatedFiles($files);
	}

	/**
	 * Delete the files related to the ones to be delete.
	 *
	 * @param Collection $deleting
	 *
	 * @return void
	 */
	protected function deleteRelatedFiles($deleting)
	{
		$files = $this->deletingFilesNamesList($deleting);

		foreach ($files as $file) {
			foreach (Storage::files($this->directory) as $relative) {
				if (strpos($relative, $file) !== false) {
					Storage::delete($relative);
				}
			}
		}
	}

	/**
	 * Returns a sanitized deleting files list.
	 *
	 * @param  Collection $files
	 *
	 * @return array
	 */
	protected function deletingFilesNamesList($files) : array
	{
		return $files->flatMap(function($item) {
			$path_parts = pathinfo($item);
			$result[] = $path_parts['filename'];

			return $result;
		})->filter(function($item) {
			return trim($item) != '';
		})->all();
	}

}
