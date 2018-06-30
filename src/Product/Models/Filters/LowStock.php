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

use Illuminate\Database\Eloquent\Builder;

class LowStock implements FilterContract
{
	/**
	 * The requested brand.
	 *
	 * @var bool
	 */
	protected $lowStock = null;

	/**
     * Create a new instance.
     *
     * @param bool $lowStock
     *
     * @return void
     */
	public function __construct($lowStock, Builder $builder)
	{
		$this->builder = $builder;
		$this->lowStock = !! $lowStock;
	}

	/**
	 * Builds the query with the given category.
	 *
	 * @return Builder
	 */
	public function query() : Builder
	{
		if ($this->lowStock) {
			$this->builder->whereRaw('stock <= low_stock');
		}

		return $this->builder;
	}
}
