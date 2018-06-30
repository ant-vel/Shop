<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Products;

use Antvel\Tests\TestCase;

class ProductsTestCase extends TestCase
{
	public function setUp()
	{
		parent::setUp();

		$this->repository = $this->app->make('Antvel\Product\Products');
	}

	protected function createProductWithPictures($attr = [], $times = 3)
	{
		$product = factory('Antvel\Product\Models\Product')->create($attr);

		for ($i=0; $i < $times; $i++) {
			factory('Antvel\Product\Models\ProductPictures')->create([
				'product_id' => $product->id,
				'path' => $this->persistentUpload('images/products')->store('images/products/' . $product->id)
			]);
		}

		return $product;
	}

	protected function data()
	{
		return [
			'category' => factory('Antvel\Categories\Models\Category')->create()->id,
			'name' => 'Foo Bar',
			'description' => 'Foo Bar Biz',
			'cost' => 849,
			'price' => 949,
			'stock' => 10,
			'low_stock' => 2,
			'brand' => 'fake brand',
			'condition' => 'new',
			'status' => true,
			'features' => [
				'weight' => '12',
				'dimensions' => '8x8x8',
				'color' => 'black',
			]
		];
	}
}
