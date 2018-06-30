<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product\Models\Concerns;

use Illuminate\Support\Collection;

trait InteractWithGroups
{
    /**
     * A product belongs to a group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function group()
    {
        return $this
            ->belongsToMany($this, 'products_grouping', 'product_id', 'associated_id')
            ->withTimestamps();
    }

    /**
     * Returns a products group parents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function groupParent()
    {
        return $this
            ->belongsToMany($this, 'products_grouping', 'associated_id', 'product_id')
            ->groupBy('product_id');
    }

    /**
     * Add products to a given group.
     *
     * @param  array $products
     *
     * @return void
     */
    public function groupWith(...$products)
    {
        $products = Collection::make($products)->flatten()->all();

        foreach ($products as $product) {
            if (! $this->hasGroup($product)) {
                $this->group()->attach($product);
            }
        }
    }

    /**
     * Checks whether the given product has the associated product in its group.
     *
     * @param  self|int $associated
     *
     * @return boolean
     */
    public function hasGroup($associated) : bool
    {
        $associated_id = $associated instanceof $this ? $associated->id : $associated;

        return !! $this->group()->where('associated_id', $associated_id)->exists();
    }

    /**
     * Delete products from a given group.
     *
     * @param  array $products
     *
     * @return void
     */
    public function ungroup(...$products)
    {
        foreach ($products as $product) {
            $this->group()->detach($product);
        }
    }
}
