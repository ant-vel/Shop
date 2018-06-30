<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Categories\Repositories;

use Illuminate\Support\Facades\Cache;
use Antvel\Contracts\CategoryRepositoryContract;

class CategoriesCacheRepository implements CategoryRepositoryContract
{
    /**
     * The Antvel category repository implementation.
     *
     * @var CategoryRepositoryContract
     */
    protected $next = null;

    /**
     * Creates a new instance.
     *
     * @param CategoryRepositoryContract $next
     */
    public function __construct(CategoryRepositoryContract $next)
    {
        $this->next = $next;
    }

    /**
     * Returns the categories with products filtered by the given request.
     *
     * @param array $request
     * @param integer $limit
     * @param mixed $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function categoriesWithProducts(array $request = [], $limit = 10, $columns = '*')
    {
        $key = md5(vsprintf('%s', ['categories_with_products']));

        return Cache::remember($key, 25, function () use ($request, $limit, $columns) {
            return $this->next->categoriesWithProducts($request, $limit, $columns);
        });
    }

    /**
     * Returns the children for the given category.
     *
     * @param  Category|mixed $idOrModel $idOrModel
     * @param int $limit
     * @param array $columns
     *
     * @return \Illuminate/Database/Eloquent/Collection
     */
    public function childrenOf($category_id, int $limit = 50, $columns = 'id')
    {
        $key = md5(vsprintf('%s', ['children_of_category']));

        return Cache::remember($key, 25, function () use ($category_id, $limit, $columns) {
            return $this->next->childrenOf($category_id, $limit, $columns);
        });
    }
}
