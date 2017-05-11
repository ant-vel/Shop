<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\User;

use Illuminate\Support\Collection;

class Preferences
{
	/**
	 * The allowed schema for users preferences.
	 *
	 * @var array
	 */
	protected $allowed = [
		'my_searches' => '',
		'product_shared' => '',
		'product_viewed' => '',
		'product_purchased' => '',
		'product_categories' => '',
	];

	/**
	 * The user preferences.
	 *
	 * @var string
	 */
	protected $preferences = null;

	/**
	 * Creates a new instance from a given user preferences.
	 *
	 * @param  mixed $preferences
	 *
	 * @return self
	 */
	public static function parse($preferences = null)
	{
		$static = new static;

		$static->allowed = Collection::make($static->allowed);

		$static->preferences = $static->prune($preferences);

		return $static;
	}

	/**
	 * Prunes the given preferences.
	 *
	 * @param  mixed $preferences
	 *
	 * @return Collection
	 */
	protected function prune($preferences = null) : Collection
	{
		$preferences = $this->normalizePref($preferences);

		return Collection::make($preferences)->filter(function($item, $key) {
			return $this->allowed->has($key);
		});
	}

	/**
	 * Normalize the given preferences.
	 *
	 * @param  mixed $preferences
	 *
	 * @return array
	 */
	protected function normalizePref($preferences = null) : array
	{
		if (is_string($preferences)) {
			return json_decode($preferences, true);
		}

		if (is_null($preferences) || count($preferences) == 0) {
			return $this->allowed->all();
		}

		return $preferences;
	}

	/**
	 * Updates the user preferences for a given key and data.
	 *
	 * @param  string $key
	 * @param  mixed $data
	 *
	 * @return self
	 */
	public function update(string $key, $data)
	{
		if ($this->allowed->has($key)) {
			$this->updateReferencesForKey(
				$key, $data->pluck('tags')
			);

			$this->updateCategories(
				$data->pluck('category_id')->unique()
			);
		}

		return $this;
	}

	/**
	 * Updates the user references for a given key.
	 *
	 * @param  string $key
	 * @param  Collection $tags
	 *
	 * @return void
	 */
	protected function updateReferencesForKey(string $key, Collection $tags)
	{
		$preferences = $this->preferences[$key];

		$preferences = is_array($preferences) ? $preferences : explode(',', $preferences ?? '');

		$this->preferences[$key] = $this->parseTags($tags)
			->merge($preferences)
			->unique()
			->implode(',');
	}

	/**
	 * Parse the given tags collection.
	 *
	 * @param Collection $tags
	 *
	 * @return Collection
	 */
	protected function parseTags(Collection $tags) : Collection
	{
		$tags = str_replace('"', '', $tags->implode(','));

		return Collection::make(
			explode(',', $tags)
		)->unique();
	}

	/**
	 * Updates the user categories key with the given collection.
	 *
	 * @param  Collection $data
	 *
	 * @return void
	 */
	protected function updateCategories(Collection $data)
	{
		$categories = $this->preferences['product_categories'];

		$current = is_array($categories) ? $categories : explode(',', $categories ?? '');

		$this->preferences['product_categories'] = Collection::make($current)
			->merge($data)
			->unique()
			->implode(',');
	}

	/**
	 * Cast the user preferences to an array.
	 *
	 * @return array
	 */
	public function toArray() : array
	{
		return $this->preferences->all();
	}

	/**
	 * Cast the user preferences to json.
	 *
	 * @return string
	 */
	public function toJson() : string
	{
		return json_encode($this->preferences->all());
	}

	/**
	 * Plucks the given key from the user preferences.
	 *
	 * @param  string $key
	 *
	 * @return Collection
	 */
	public function pluck($key) : Collection
	{
		if (! $this->preferences->has($key)) {
			return new Collection;
		}

		return Collection::make(
			explode(',', $this->preferences[$key])
		);
	}
}
