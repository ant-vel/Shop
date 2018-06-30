<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product\Models;

use Illuminate\Database\Eloquent\Builder;

class SuggestionQuery
{
	/**
	 * The laravel database builder.
	 *
	 * @var null
	 */
	protected $builder = null;

	/**
	 * The type of the query.
	 *
	 * @var null
	 */
	protected $type = null;

	/**
     * Create a new instance.
     *
     * @param array $builder
     *
     * @return void
     */
	public function __construct(Builder $builder)
	{
		$this->builder = $builder->distinct()->actives();
	}

	/**
	 * Sets the type of query.
	 *
	 * @param  string $type
	 *
	 * @return self
	 */
	public function type(string $type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Suggest products based on the given tags.
	 *
	 * @param  array $tags
	 *
	 * @return Builder
	 */
	public function apply($constraints) : Builder
	{
		if ($constraints && $this->type == 'product_categories') {
			return $this->builder->whereIn('category_id', $constraints);
		}

		if ($constraints && is_array($constraints)) {
			return $this->filterByConstraints($constraints);
		}

		return $this->builder;
	}

	/**
	 * Filter the query by the given constraints.
	 *
	 * @param  array $constraints
	 *
	 * @return Builder
	 */
	protected function filterByConstraints($constraints) : Builder
	{
		$this->builder->where(function($query) use ($constraints) {
			foreach ($constraints as $filter) {
				if (trim($filter) != '') {
					$query->orWhere('tags', 'like', '%' . $filter . '%');
				}
			}

			return $query;
		});

		return $this->builder;
	}
}
