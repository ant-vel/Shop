<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product\Repositories;

use Antvel\Product\Models\Product;

class ProductsRepository
{
	/**
	 * It increments the given counter in the model.
	 *
	 * @param  string $field
	 * @param  Product $product
	 *
	 * @return void
	 */
	public function increment(string $field, Product $product)
	{
		$product->increment($field);
	}

	/**
	 * It decrements the given counter in the model.
	 *
	 * @param  string $field
	 * @param  Product $product
	 *
	 * @return void
	 */
	public function decrement(string $field, Product $product)
	{
		$product->decrement($field);
	}
}
