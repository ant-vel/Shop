<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product\Models\Filters;

use Illuminate\Support\Arr;
use Antvel\Categories\Normalizer;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class Category implements FilterContract
{
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
		$category = explode('|', $input);

		if (isset($category[0]) && trim($category[0]) != '') {
			$this->category_id = urldecode($category[0]);
		}

		if (isset($category[1]) && trim($category[1]) != '') {
			$this->category_name = urldecode($category[1]);
		}
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
			$this->builder->whereIn('category_id', $children);
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
		$categories = App::make('category.repository.cahe')->childrenOf($this->category_id, 50, [
			'id', 'category_id', 'name'
		]);

		return Normalizer::generation($categories)
        	->prepend((int) $this->category_id)
        	->all();
	}
}
