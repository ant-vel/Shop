<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Features\Repositories;

use Illuminate\Support\Facades\Cache;
use Antvel\Contracts\FeaturesRepositoryContract;

class FeaturesCacheRepository implements FeaturesRepositoryContract
{
    /**
     * The Antvel features repository implementation.
     *
     * @var FeaturesRepositoryContract
     */
    protected $next = null;

    /**
     * The cache storing timing.
     * It is currently set for 2 weeks.
     *
     * @var integer
     */
    protected $remembering = 20160;

    /**
     * Creates a new instance.
     *
     * @param FeaturesRepositoryContract $next
     */
    public function __construct(FeaturesRepositoryContract $next)
    {
        $this->next = $next;
    }

    /**
     * Exposes the features allowed to be in the products filtering.
     *
     * @param  integer $limit
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterable($limit = 5)
    {
        $key = md5(vsprintf('%s', ['filterable_features']));

        return Cache::remember($key, $this->remembering, function () use ($limit) {
            return $this->next->filterable($limit);
        });
    }

	/**
     * Returns an array with the validation rules for the filterable features.
     *
     * @return array
     */
    public function filterableValidationRules() : array
    {
        $key = md5(vsprintf('%s', ['features_validationRules']));

        return Cache::remember($key, $this->remembering, function () {
            return $this->next->filterableValidationRules();
        });
    }
}
