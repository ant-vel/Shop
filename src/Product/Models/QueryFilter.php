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

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class QueryFilter
{
	/**
     * The request information.
     *
     * @var array
     */
    protected $request = null;

    /**
	 * The allowed filters.
	 *
	 * @var array
	 */
	protected $allowed = [
        'search' => Filters\Search::class,
        'category' => Filters\Category::class,
        'conditions' => Filters\Conditions::class,
        'brands' => Filters\Brands::class,
		'min' => Filters\Prices::class,
        'max' => Filters\Prices::class,
        'inactives' => Filters\Inactives::class,
        'low_stock' => Filters\LowStock::class,
		'features' => Filters\Features::class,
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
        $request = Collection::make($request);

        $allowed = $request->filter(function ($item) { return trim($item) != ''; })->only(array_keys($this->allowed));

        if ($filterable = $this->allowedFeatures($request)) {
            $allowed['features'] = $filterable;
        }

        return $allowed->all();
    }

    /**
     * Returns an array with the allowed features.
     *
     * @param  Collection $request
     *
     * @return array
     */
    public function allowedFeatures(Collection $request) : array
    {
        return $request->filter(function ($item, $key) {
            return trim($item) != '' && $this->isFilterable($key);
        })->all();
    }

    /**
     * Checks whether a given feature is filterable.
     *
     * @param  string $key
     *
     * @return bool
     */
    protected function isFilterable($key) : bool
    {
        return App::make('product.features.repository.cahe')->filterable()->pluck('name')->contains($key);
    }

    /**
     * Returns the parsed incoming request.
     *
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
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
                $this->resolveQueryFor($builder, $filter);
        	}
        }

        return $builder;
    }

    /**
     * Checks whether the request has values.
     *
     * @return boolean
     */
    public function hasRequest() : bool
    {
        return count($this->request) > 0;
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

        $input = $this->wantsByPrices($filter)
            ? $this->prices()
            : $this->request[$filter];

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
        return in_array($filter, ['min', 'max']);
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
