<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Features\Listeners;

use Antvel\Features\Parser;
use Antvel\Product\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Antvel\Features\Events\FeatureNameWasUpdated;

class UpdateFeatureName implements ShouldQueue
{
	/**
     * Handle the event.
     *
     * @param  FeatureNameWasUpdated  $event
     *
     * @return void
     */
    public function handle(FeatureNameWasUpdated $event)
    {
        $attributes = Parser::replaceTheGivenKeyFor(
            $products = $this->products($event->feature->name),
            $event->feature->name,
            $event->updatedName
        );

        $this->updateProductsFeatures(
            $attributes, $products
        );

        $event->feature->update(['name' => $event->updatedName]);
    }

    /**
     * Returns a products list for the given feature key.
     *
     * @param  string $key
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function products(string $key)
    {
        return Product::byFeaturesKey($key)->get();
    }

    /**
     * Update the given products features with the passed attributes.
     *
     * @param  array $attributes
     * @param  \Illuminate\Database\Eloquent\Collection $products
     *
     * @return void
     */
    protected function updateProductsFeatures(array $attributes, $products)
    {
        $products->each(function ($item, $key) use ($attributes, $products) {
            $item->update(['features' => json_encode($attributes[$item->id])]);
            $item->save();
        });
    }
}
