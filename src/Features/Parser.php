<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Features;

use Illuminate\Support\Collection;

class Parser
{
	/**
	 * Parses the given features to Json.
	 *
	 * @param  array|Collection $features
	 *
	 * @return null|string
	 */
	public static function toJson($features)
	{
		if (is_null($features) || count($features) == 0) {
			return null;
		}

		return Collection::make($features)->filter(function ($item) {
			return trim($item) != '';
		})->toJson();
	}

	/**
	 * Replaces the given key for another in the provided products features collection.
	 *
	 * @param  Collection $products
	 * @param  string $oldKey
	 * @param  string $newKey
	 *
	 * @return Collection
	 */
	public static function replaceTheGivenKeyFor($products, $oldKey, $newKey) : array
	{
		return $products->mapWithKeys(function ($item) use ($oldKey, $newKey) {

			$features = $item->features;

			$features[$newKey] = $features[$oldKey];
			unset($features[$oldKey]);

			return [$item->id => $features];
		})->all();
	}
}
