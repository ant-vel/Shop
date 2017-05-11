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

class FiltersParserTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();

		$this->category = factory('Antvel\Categories\Models\Category', 'child')->create();
	}

	public function test_it_parses_the_category_filters_for_a_given_collection()
	{
		$products = factory(Product::class, 2)->create([
			'category_id' => $this->category->id
		]);

		$filters = Filters::parse($products);

		$_category = reset($filters['category']);

		$this->assertTrue($_category['qty'] == 2);
		$this->assertArrayHasKey('category', $filters);
		$this->assertTrue(strcasecmp($_category['name'], 'child') == 0);
	}

	public function test_it_parses_the_brands_filters_for_a_given_collection()
	{
		$products = factory(Product::class, 2)->create([
			'category_id' => $this->category->id,
			'brand' => 'Apple'
		]);

		$filters = Filters::parse($products);

		$this->assertTrue($filters['brands']['Apple'] == 2);
		$this->assertTrue(isset($filters['brands']['Apple']));
	}

	public function test_it_parses_the_conditions_filters_for_a_given_collection()
	{
		$products = factory(Product::class, 2)->create([
			'category_id' => $this->category->id,
			'condition' => 'new'
		]);

		$filters = Filters::parse($products);

		$this->assertTrue($filters['conditions']['new'] == 2);
		$this->assertTrue(isset($filters['conditions']['new']));
	}

	public function test_it_parses_the_features_filters_for_a_given_collection()
	{
		$products = factory(Product::class, 2)->create([
			'category_id' => $this->category->id,
			'features' => '{"color": "olive", "weight": "115 Mg", "dimensions": "2 X 19 X 22 inch"}'
		]);

		$filters = Filters::parse($products);

		$this->assertTrue($filters['color']['olive'] == 2);
		$this->assertFalse(isset($filters['dimensions']));
		$this->assertFalse(isset($filters['weight']));
	}
}
