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
use Illuminate\Support\Facades\App;

class Filters
{
	/**
	 * The allowed features to be in products listing.
	 *
	 * @var array
	 */
	protected $allowed = [];

	/**
	 * The products list under evaluation.
	 *
	 * @var \Illuminate/Database/Eloquent/Collection
	 */
	protected $products = null;

	/**
	 * Cretaes a new instance.
	 *
	 * @param \Illuminate\Database\Eloquent\Collection
	 *
	 * @return void
	 */
	public function __construct($products)
	{
		$this->products = $products;

		$this->allowed = $this->allowed();
	}

	/**
	 * Returns the allowed features to be in products listing.
	 *
	 * @return array
	 */
	protected function allowed() : array
	{
		return App::make('product.features.repository.cahe')
			->filterable()
			->pluck('name')
			->all();
	}

	/**
	 * Parses the given collection.
	 *
	 * @param \Illuminate\Database\Eloquent\Collection $products
	 *
	 * @return array
	 */
	public static function parse($products) : array
	{
		$parser = new static ($products);

		return $parser->all();
	}

	/**
	 * Returns the parsed filters.
	 *
	 * @return array
	 */
	protected function all() : array
	{
		return array_merge([
			'category' => $this->forCategories(),
			'brands' => array_count_values($this->products->pluck('brand')->all()),
			'conditions' => array_count_values($this->products->pluck('condition')->all()),
		], $this->forFeatures());
	}

	/**
	 * Parses the category filter.
	 *
	 * @return array
	 */
	protected function forCategories() : array
	{
		$categories = $this->products->pluck('category');

		return $categories->mapWithKeys(function ($item) use ($categories) {

				$result[$item->id] = [
					'id' => $item->id,
					'name' => $item->name,
					'qty' => $categories->where('id', $item->id)->count()
				];

				return $result;
		})->all();
	}

	/**
	 * Returns the mapped features with their quantities.
	 *
	 * @return array
	 */
	protected function forFeatures() : array
	{
		return Collection::make($this->allowed)->mapWithKeys(function ($feature) {
        	return [
        		$feature => array_count_values($this->features()[$feature])
        	];
        })->all();
	}

	/**
	 * Returns a map with the given features.
	 *
	 * @return array
	 */
	protected function features() : array
	{
		$features = $this->products->pluck('features');

		return Collection::make($this->allowed)->mapWithKeys(function ($allowed) use ($features) {

			return [
				$allowed => $features->pluck($allowed)->filter(function ($allowed) {
					return ! is_null($allowed);
				})->all()
			];

		})->all();
	}
}
