<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product\Filters;

use Cache;
use Illuminate\Support\Arr;
use Antvel\Categories\Categories;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;

class Category
{
	/**
	 * The categories repository.
	 *
	 * @var Categories
	 */
	protected $categories = null;

	/**
	 * The Illuminate eloquent builder.
	 *
	 * @var Builder
	 */
	protected $builder = null;

	/**
	 * The requested category ID.
	 *
	 * @var int
	 */
	protected $category_id = null;

	/**
	 * The requested category name.
	 *
	 * @var string
	 */
	protected $category_name = null;

	/**
     * Create a new instance.
     *
     * @param string $input
     *
     * @return void
     */
	public function __construct(string $input, Builder $builder)
	{
		$this->parseInput($input);
		$this->builder = $builder;
		$this->categories = Container::getInstance()->make(Categories::class);
	}

	/**
	 * Parses the given category info.
	 *
	 * @param  string $input
	 *
	 * @return void
	 */
	protected function parseInput(string $input)
	{
		$category = explode('|', urldecode($input));

		$this->category_name = Arr::last($category);
		$this->category_id = Arr::first($category);
	}

	/**
	 * Builds the query with the given category.
	 *
	 * @return Builder
	 */
	public function query() : Builder
	{
		if (is_null($this->category_id)) {
			return $this->builder;
		}

		if (count($children = $this->children()) > 0) {
			$this->builder->whereIn(
				'category_id', $children
			);
		}

		return $this->builder;
	}

	/**
	 * Returns the children for a given category.
	 *
	 * @return array
	 */
	protected function children() : array
	{
		$categories = Cache::remember($this->cache_key(), 15, function () {
			return $this->categories->children(
	            $this->category_id, ['id', 'category_id', 'name']
	        );
		});

		return $categories->pluck('id')
        	->prepend((int) $this->category_id)
        	->all();
	}

	/**
	 * Returns the filter cache key.
	 *
	 * @return string
	 */
	protected function cache_key() : string
	{
		return 'fitered_by_category_' . $this->category_id;
	}
}
