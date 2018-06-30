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

class Features implements FilterContract
{
	/**
	 * The requested features.
	 *
	 * @var array
	 */
	protected $features = [];

	/**
     * Create a new instance.
     *
     * @param array $features
     *
     * @return void
     */
	public function __construct(array $features, Builder $builder)
	{
		$this->features = $features;
		$this->builder = $builder;
	}

	/**
	 * Builds the query with the given category.
	 *
	 * @return Builder
	 */
	public function query() : Builder
	{
		return $this->builder->where(
			$this->resolveQuery()
		);
	}

	/**
	 * Resolve the query for the requested features.
	 *
	 * @return callable
	 */
	protected function resolveQuery() : callable
	{
		return function($query) {
			foreach ($this->features as $key => $feature) {
				$query->orWhere('features->' . $key, urldecode($feature));
			}
		};
	}
}
