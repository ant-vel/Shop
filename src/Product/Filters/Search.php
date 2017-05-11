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

use Illuminate\Database\Eloquent\Builder;

class Search
{
	/**
	 * The requested seed.
	 *
	 * @var int
	 */
	protected $seed = null;

	/**
	 * The fields where to perform the searching.
	 *
	 * @var array
	 */
	protected $searchable = ['name', 'description', 'features', 'brand', 'tags'];

	/**
     * Create a new instance.
     *
     * @param string $seed
     *
     * @return void
     */
	public function __construct(string $seed, Builder $builder)
	{
		$this->seed = $seed;
		$this->builder = $builder;
	}

	/**
	 * Builds the query with the given category.
	 *
	 * @return Builder
	 */
	public function query() : Builder
	{
		if (trim($seed = $this->seed) !== '') {
			$this->builder->where(function ($query) use ($seed) {
				return $this->resolveQuery($query, $seed);
			});
		}

		return $this->builder;
	}

	/**
	 * Resolves the query for the given seed.
	 *
	 * @param  Builder $builder
	 * @param  string  $seed
	 *
	 * @return Builder
	 */
	protected function resolveQuery(Builder $builder, string $seed) : Builder
	{
		foreach ($this->searchable as $field) {
			$builder->orWhere($field, 'like', '%'.$seed.'%');
		}

		return $builder;
	}
}
