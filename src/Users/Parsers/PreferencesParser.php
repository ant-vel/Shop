<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Users\Parsers;

use Illuminate\Support\Collection;
use Illuminate\Container\Container;

class PreferencesParser
{
	/**
	 * The maximum quantity allowed of tags per key.
	 */
	const MAX_TAGS = 50;

	/**
	 * The laravel auth component.
	 *
	 * @var Authenticable
	 */
	protected $auth = null;

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
	 * @return void
	 */
	public function __construct()
	{
		$this->allowed = Collection::make($this->allowed);
		$this->auth = Container::getInstance()->make('auth');
	}

	/**
	 * Returns a collection with the allowed keys.
	 *
	 * @return Collection
	 */
	public static function allowed()
	{
		$static = new static;

		return $static->allowed->keys();
	}

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

		$static->preferences = $static->sanitize($preferences);

		return $static;
	}

	/**
	 * Sanitizes the given preferences.
	 *
	 * @param  null|string $preferences
	 *
	 * @return Collection
	 */
	protected function sanitize($preferences = null) : Collection
	{
		if (is_null($preferences) && $this->auth->check()) {
			return Collection::make($this->auth->user()->preferences);
		}

		if (is_null($preferences) && ! $this->auth->check()) {
			return $this->allowed;
		}

		if (is_string($preferences)) {
			$preferences = json_decode($preferences, true);
		}

		return Collection::make($preferences)->filter(function($item, $key) {
			return $this->allowed->has($key);
		});
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
		if (is_string($data)) {
			$data = $this->normalizeToCollection($data);
		}

		if ($this->allowed->has($key)) {
			$this->updatePreferencesForKey(
				$key, $this->normalizedTags($data)
			);

			$this->updateCategories(
				$data->pluck('category_id')->unique()
			);
		}

		return $this;
	}

	/**
	 * Returns a formatted collection from the given string.
	 *
	 * @param  string $tags
	 *
	 * @return Collection
	 */
	protected function normalizeToCollection(string $tags) : Collection
	{
		return Collection::make([
			'tags' => explode(',', $tags)
		]);
	}

	/**
	 * Returns a collection of tags.
	 *
	 * @param  mixed $data
	 *
	 * @return Collection
	 */
	protected function normalizedTags($data) : Collection
	{
		return $data->has('tags')
			? Collection::make($data->get('tags'))
			: $data->pluck('tags');
	}

	/**
	 * Updates the user references for a given key.
	 *
	 * @param  string $key
	 * @param  Collection $tags
	 *
	 * @return void
	 */
	protected function updatePreferencesForKey(string $key, Collection $tags)
	{
		$tags = str_replace('"', '', $tags->implode(','));

		$tags = Collection::make(explode(',', $tags))
			->merge(explode(',',$this->preferences[$key]))
			->unique()
			->take(self::MAX_TAGS)
			->implode(',');

		$this->preferences[$key] = rtrim($tags, ',');
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
		$ids = explode(',', $this->preferences['product_categories']);

		$categories = Collection::make($ids)
			->merge($data)
			->unique()
			->take(self::MAX_TAGS)
			->implode(',');

		$this->preferences['product_categories'] = trim($categories, ',');
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

	/**
	 * Takes the given keys from the preferences array.
	 *
	 * @param  string|array $keys
	 *
	 * @return Collection
	 */
	public function all($keys = []) : Collection
	{
		if (count($keys) == 0) {
			$keys = $this->preferences->keys();
		}

		if (is_string($keys)) {
			$keys = [$keys];
		}

		return Collection::make($keys)->flatMap(function ($item) {
			if (isset($this->preferences[$item])) {
				$result[$item] = explode(',', $this->preferences[$item]);
			}
			return $result ?? [];
		});
	}
}
