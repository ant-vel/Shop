<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\ProductsFilters;

use Antvel\Tests\TestCase;
use Antvel\Product\Models\Product;
use Antvel\Product\Parsers\Filters;

class SuggestionsTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();

		$this->repository = $this->app->make('Antvel\Product\Products');

		$this->category = factory('Antvel\Categories\Models\Category', 'child')->create();
	}

	public function test_it_can_suggest_products_based_on_a_given_collection()
	{
		$products = factory(Product::class, 2)->create([
			'category_id' => $this->category->id,
			'tags' => 'foo,bar'
		]);

		$products2 = factory(Product::class, 2)->create([
			'tags' => 'home,phone,bar'
		]);

		$results = $this->repository->filter([
			'category' => $this->category->id .'|'.$this->category->name
		]);

		$suggestion = $this->repository->suggestFor($results);

		$results = $results->pluck('id')->toArray();

		$this->assertTrue(count($suggestion) == 2);
		$this->assertTrue(count($suggestion->whereIn('id', $results)) == 0);
	}
}
