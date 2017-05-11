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

class Prices
{
	/**
	 * The minimum amount requested.
	 *
	 * @var double
	 */
	protected $min = null;

	/**
	 * The maximum amount requested.
	 *
	 * @var double
	 */
	protected $max = null;

	/**
     * Create a new instance.
     *
     * @param array $prices
     *
     * @return void
     */
	public function __construct(array $prices, Builder $builder)
	{
		$this->min = $prices['min'];
		$this->max = $prices['max'];
		$this->builder = $builder;
	}

	/**
	 * Builds the query with the given category.
	 *
	 * @return Builder
	 */
	public function query() : Builder
	{
		if (! is_null($this->min) && ! is_null($this->max)) {
			$this->builder->whereBetween('price', [$this->min, $this->max]);
		}

		if (! is_null($this->min) && is_null($this->max)) {
			$this->builder->where('price', '>=', $this->min);
		}

		if (is_null($this->min) && ! is_null($this->max)) {
			$this->builder->where('price', '<=', $this->max);
		}

		return $this->builder;
	}
}
