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

class Filters
{
	/**
	 * The features to be excluded during a given search.
	 *
	 * @var array
	 *
	 */
	protected $excludedFilters = ['images', 'dimensions', 'weight', 'brand'];

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
	 * Returns the mapped features with their quantities.
	 *
	 * @return array
	 */
	protected function forFeatures() : array
	{
		$filters = [];

		$features = $this->mapFeatures(
			$this->products->pluck('features')
		);

		foreach ($features as $key => $value) {
        	foreach ($features[$key] as $row) {
				if (is_string($row)) {
                    $filters[$key][$row] = isset($filters[$key][$row]) ? $filters[$key][$row] + 1 : 1;
                }
            }
        }

        return $filters;
	}

	/**
	 * Returns a map with the given features.
	 *
	 * @param Illuminate\Support\Collection $features
	 *
	 * @return array
	 */
	protected function mapFeatures($features) : array
	{
		$map = [];

		foreach ($features as $feature) {
            $feature = Collection::make($feature)->except($this->excludedFilters);
            foreach ($feature as $key => $value) {
                $map[$key][] = $value;
            }
        }

        return $map;
	}

	/**
	 * Parses the category filter.
	 *
	 * @return array
	 */
	protected function forCategories() : array
	{
		$counting = $this->categoriesCountValues();

		return $counting->mapWithKeys( function($item, $key) {
			return [
				$key => [
					'id' => $key,
					'name' => $this->categories()[$key],
					'qty' => $item
				]
			];
		})->all();
	}

	/**
	 * Map the given categories with the total of products associated with it.
	 *
	 * @return Collection
	 */
	protected function categoriesCountValues() : Collection
	{
		$counting = array_count_values(
			$this->products->pluck('category_id')->all()
		);

		return Collection::make($counting)->sort();
	}

	/**
	 * The categories array returned by the products query.
	 *
	 * @return Collection
	 */
	protected function categories() : Collection
	{
		return $this->products
			->pluck('category.id', 'category.name')
			->flip();
	}
}
