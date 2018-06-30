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

class Inactives implements FilterContract
{
	/**
	 * The requested brand.
	 *
	 * @var bool
	 */
	protected $inactives = null;

	/**
     * Create a new instance.
     *
     * @param bool $inactives
     *
     * @return void
     */
	public function __construct($inactives, Builder $builder)
	{
		$this->builder = $builder;
		$this->inactives = !! $inactives;
	}

	/**
	 * Builds the query with the given category.
	 *
	 * @return Builder
	 */
	public function query() : Builder
	{
		if ($this->inactives) {
			$this->builder->where('status', 0);
		}

		return $this->builder;
	}
}
