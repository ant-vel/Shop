<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Categories;

use Illuminate\Support\Collection;

class Normalizer
{
	/**
	 * Returns the generation ids for the given categories.
	 *
	 * @param  Collection $categories
	 * @return array
	 */
	public static function generation(Collection $categories) : Collection
	{
		$ids = [];

		foreach ($categories as $category) {
			$ids[] = $category->id;
			$ids[] = static::familyTree($category->childrenRecursive);
		}

        return Collection::make($ids)->flatten()->unique()->sort();
	}

	/**
	 * Returns the family tree ids for the given categories.
	 *
	 * @param  Collection $categories
	 * @return array
	 */
	protected static function familyTree(Collection $categories) : array
	{
		$ids = isset($ids) && count($ids) > 0 ? $ids : [];

        foreach ($categories as $category) {
            $ids[] = $category->id;
            if ($category->childrenRecursive->count() > 0) {
                $ids[] = static::familyTree($category->childrenRecursive);
            }
        }

        return $ids;
	}
}
