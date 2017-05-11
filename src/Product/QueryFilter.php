<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class QueryFilter
{
	/**
     * The request information.
     *
     * @var Illuminate\Http\Request
     */
    protected $request = null;

    /**
	 * The allowed filters.
	 *
	 * @var array
	 */
	protected $allowed = [
        'search' => \Antvel\Product\Filters\Search::class,
        'category' => \Antvel\Product\Filters\Category::class,
        'conditions' => \Antvel\Product\Filters\Conditions::class,
        'brands' => \Antvel\Product\Filters\Brands::class,
		'min' => \Antvel\Product\Filters\Prices::class,
		'max' => \Antvel\Product\Filters\Prices::class
	];

    /**
     * Create a new instance.
     *
     * @param array $request
     *
     * @return void
     */
    public function __construct(array $request)
    {
    	$this->request = $this->parseRequest($request);

        // $this->request['search'] = 'Seeded'; //while testing
    }

    /**
     * Parses the incoming request.
     *
     * @param array $request
     *
     * @return array
     */
    protected function parseRequest(array $request) : array
    {
    	$allowed = Collection::make($this->allowed)->keys()->all();

        return Collection::make($request)->filter(function($item) {
            return ! is_null($item);
        })->only($allowed)->all();
    }

    /**
     * Apply an eloquent query to a given builder.
     *
     * @param  Builder $builder
     *
     * @return Builder
     */
    public function apply(Builder $builder) : Builder
    {
        if ($this->hasRequest()) {
            foreach ($this->request as $filter => $value) {
                if ($this->isQueryableBy($filter)) {
        			$builder = $this->resolveQueryFor($builder, $filter);
        		}
        	}
        }

        return $builder;
    }

    /**
     * Checks whether the request has values.
     *
     * @return boolean
     */
    protected function hasRequest() : bool
    {
        return count($this->request) > 0;
    }

    /**
     * Checks whether a given filter can query.
     *
     * @param  string $filter
     *
     * @return bool
     */
    protected function isQueryableBy($filter) : bool
    {
    	return !! isset($this->allowed[$filter]) && method_exists($this->allowed[$filter], 'query');
    }

    /**
     * Returns a query filtered by the given filter.
     *
     * @param Builder $builder
     * @param string $key
     *
     * @return Builder
     */
    protected function resolveQueryFor(Builder $builder, string $filter) : Builder
    {
        $factory = $this->allowed[$filter];

        $input = $this->wantsByPrices($filter) ? $this->prices() : $this->request[$filter];

    	return (new $factory($input, $builder))->query();
    }

    /**
     * Checks whether the request is by prices.
     *
     * @param  string $filter
     *
     * @return bool
     */
    protected function wantsByPrices(string $filter) : bool
    {
        return $filter == 'min' || $filter == 'max';
    }

    /**
     * Returns the requested prices filter.
     *
     * @return array
     */
    protected function prices() : array
    {
        return [
            'min' => Arr::get($this->request, 'min'),
            'max' => Arr::get($this->request, 'max'),
        ];
    }
}
