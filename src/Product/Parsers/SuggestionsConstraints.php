<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product\Parsers;

use Illuminate\Support\Collection;

class SuggestionsConstraints
{
	/**
	 * The data to be removed from the analyzing collection.
	 *
	 * @var array
	 */
	protected $except = [];

	/**
	 * The collection to be analyzed.
	 *
	 * @var null
	 */
	protected $items = null;

	/**
	 * Creates a new instance with the given items.
	 *
	 * @param mixed $items
	 *
	 * @return void
	 */
	public function __construct($items)
	{
		$this->items = $items;
		$this->except = $items->pluck('id')->all();
	}

	/**
	 * Completes the products results for a given key.
	 *
	 * @param string $scope
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public static function from($items)
	{
		$suggestions = new static ($items);

		return $suggestions->all();
	}

	/**
	 * Returns the suggestions.
	 *
	 * @return array
	 */
	public function all() : array
	{
		return [
			'tags' => $this->tags()->all(),
			'except' => $this->except,
		];
	}

	/**
	 * Returns the tags related to the given collection.
	 *
	 * @return Collection
	 */
	protected function tags() : Collection
	{
		return $this->items->pluck('tags')->flatMap(function ($item, $key) {
			return Collection::make(explode(',', trim($item,'"')));
		})->unique();
	}
}
