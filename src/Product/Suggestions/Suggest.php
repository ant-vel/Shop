<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product\Suggestions;

use Illuminate\Support\Collection;
use Antvel\Product\Models\Product;
use Illuminate\Support\Facades\Auth;
use Antvel\Users\Parsers\PreferencesParser;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Suggest
{
	/**
	 * user's preferences keys.
	 *
	 * @var string|array
	 */
	protected $keys = null;

	/**
	 * The user whose the suggestions are for.
	 *
	 * @var Authenticatable
	 */
	protected $user = null;

	/**
	 * The listed products ids.
	 *
	 * @var Collection
	 */
	protected $excluding = null;

	/**
	 * The number of products to be listed.
	 *
	 * @var integer
	 */
	protected $limit = 4;

	/**
	 * The base products to look for tags against.
	 *
	 * @var Collection
	 */
	protected $products = null;

	/**
	 * Creates a new instance for the given keys.
	 *
	 * @param  mixed $keys
	 *
	 * @return self
	 */
	public function __construct($keys)
	{
		$this->excluding = new Collection;
		$this->keys = Collection::make($keys);
	}

	/**
	 * Creates a new instance for the given keys.
	 *
	 * @param  mixed $keys
	 *
	 * @return self
	 */
	public static function for(...$keys)
	{
		return new static($keys);
	}

	/**
	 * Set the suggestions user.
	 *
	 * @param  Authenticatable $user
	 *
	 * @return self
	 */
	public function actingAs(Authenticatable $user)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Set the suggestions limit.
	 *
	 * @param  integer $limit
	 *
	 * @return self
	 */
	public function take(int $limit)
	{
		$this->limit = $limit;

		return $this;
	}

	/**
	 * Returns a mapped products suggestions based on the given key.
	 *
	 * @param  Collection $products
	 *
	 * @return Collection
	 */
	public function shake()
	{
		return $this->keys->flatMap(function ($preferenceKey) {

			$products[$preferenceKey] = is_string($preferenceKey) ? $this->suggestFor($preferenceKey) : new Collection;

			return $products;
		});
	}

	/**
	 * Returns a mapped products suggestions based on the given products.
	 *
	 * @param  Collection $products
	 *
	 * @return Collection
	 */
	public static function shakeFor(Collection $products) : Collection
	{
		$suggestions = new static($key = 'all');

		$suggestions->products = $products;

		$suggestions->excluding($products);

		return $suggestions->suggestFor($key);
	}

	/**
	 * Builds the excluding list based on the given collection.
	 *
	 * @param Collection $products
	 *
	 * @return void
	 */
	public function excluding(Collection $products)
	{
		$ids = $products->pluck('id');

		if ($this->excluding->count() == 0) {
			$this->excluding = $ids;
		} else {
			foreach ($ids as $id) {
				$this->excluding->push($id);
			}
		}

		return $this;
	}

	/**
	 * Returns a products suggestion for the given key.
	 *
	 * @param  string $preferenceKey
	 *
	 * @return Collection
	 */
	protected function suggestFor(string $preferenceKey) : Collection
	{
		$suggestions = Product::suggestionsFor($preferenceKey, $this->resolveTagsFor($preferenceKey))
			->whereNotIn('id', $this->excluding->all())
			->orderBy('rate_val', 'desc')
			->take($this->limit)
			->get();


		if ($suggestions->count() < $this->limit) {
			$suggestions = $suggestions->merge($this->completeWithRandomProducts($suggestions));
		}

		$this->excluding($suggestions);

		return $suggestions;
	}

	/**
	 * Completes the returned suggestion with random products.
	 *
	 * @param  Collection $products
	 *
	 * @return Collection
	 */
	protected function completeWithRandomProducts(Collection $products) : Collection
	{
		$currentLimit = $this->limit - $products->count();

		return Product::whereNotIn('id', $this->excluding->all())
			->orderBy('rate_val', 'desc')
			->take($currentLimit)
			->get();
	}

	/**
	 * Resolves the tags list for the given key.
	 *
	 * @param  string $preferenceKey
	 *
	 * @return array
	 */
	protected function resolveTagsFor(string $preferenceKey) : array
	{
		if (! is_null($this->products)) {
			return $this->pullTagsFromProducts();
		}

		$preferences = $this->resolveUserPreferences();

		$tags = PreferencesParser::parse($preferences)->all($this->keys);

		return isset($tags[$preferenceKey]) ? $tags[$preferenceKey] : [];
	}

	/**
	 * Returns an array with tags extracted from the products list.
	 *
	 * @return array
	 */
	protected function pullTagsFromProducts() : array
	{
		return $this->products->map(function ($item) {
			return explode(',', str_replace('"', '', $item->tags));
		})->flatten()->unique()->all();
	}

	/**
	 * Resolve the user's preferences tags.
	 *
	 * @return null/array
	 */
	protected function resolveUserPreferences()
	{
		if ($this->user) {
			return $this->user->preferences;
		}

		if (Auth::check()) {
			return Auth::user()->preferences;
		}

		return null;
	}
}
