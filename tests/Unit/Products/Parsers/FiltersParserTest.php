<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Products\Parsers;

use Antvel\Tests\TestCase;
use Antvel\Product\Models\Product;
use Antvel\Product\Parsers\Filters;

class FiltersParserTest extends TestCase
{
	public function test_it_parses_the_category_filters_for_a_given_collection()
	{
		$category_01 = factory('Antvel\Categories\Models\Category')->create(['name' => 'foo']);
		$category_02 = factory('Antvel\Categories\Models\Category')->create(['name' => 'bar']);
		$category_03 = factory('Antvel\Categories\Models\Category')->create(['name' => 'biz']);
		$category_04 = factory('Antvel\Categories\Models\Category', 'child')->create();

		$products = collect([
			factory(Product::class)->create(['category_id' => $category_01->id]),
			factory(Product::class)->create(['category_id' => $category_02->id]),
			factory(Product::class)->create(['category_id' => $category_02->id]),
			factory(Product::class)->create(['category_id' => $category_03->id]),
			factory(Product::class)->create(['category_id' => $category_04->id]),
			factory(Product::class)->create(['category_id' => $category_04->id]),
		]);

		$filters = Filters::parse($products);
		$categories = $filters['category'];

		$this->assertTrue(is_array($filters));
		$this->assertTrue(count($filters) > 0);
		$this->assertArrayHasKey('category', $filters);

		$this->assertTrue(isset($categories[$category_01->id]));
		$this->assertSame($categories[$category_01->id]['qty'], 1);
		$this->assertSame($categories[$category_01->id]['name'], 'foo');

		$this->assertTrue(isset($categories[$category_02->id]));
		$this->assertSame($categories[$category_02->id]['qty'], 2);
		$this->assertSame($categories[$category_02->id]['name'], 'bar');

		$this->assertTrue(isset($categories[$category_03->id]));
		$this->assertSame($categories[$category_03->id]['qty'], 1);
		$this->assertSame($categories[$category_03->id]['name'], 'biz');

		$this->assertTrue(isset($categories[$category_04->id]));
		$this->assertSame($categories[$category_04->id]['qty'], 2);
		$this->assertSame($categories[$category_04->id]['name'], 'child');
	}

	public function test_it_parses_the_brands_filters_for_a_given_collection()
	{
		$products = collect([
			factory(Product::class)->make(['brand' => 'foo']),
			factory(Product::class)->make(['brand' => 'foo']),
			factory(Product::class)->make(['brand' => 'bar']),
			factory(Product::class)->make(['brand' => 'biz']),
		]);

		$filters = Filters::parse($products);

		$this->assertTrue(is_array($filters));
		$this->assertTrue(count($filters) > 0);
		$this->assertArrayHasKey('brands', $filters);

		tap($filters['brands'], function ($brands) {
			$this->assertSame($brands['foo'], 2);
			$this->assertSame($brands['bar'], 1);
			$this->assertSame($brands['biz'], 1);
		});
	}

	public function test_it_parses_the_conditions_filters_for_a_given_collection()
	{
		$products = collect([
			factory(Product::class)->make(['condition' => 'new']),
			factory(Product::class)->make(['condition' => 'new']),
			factory(Product::class)->make(['condition' => 'used']),
			factory(Product::class)->make(['condition' => 'refurbished']),
		]);

		$filters = Filters::parse($products);

		$this->assertTrue(is_array($filters));
		$this->assertTrue(count($filters) > 0);
		$this->assertArrayHasKey('conditions', $filters);

		tap($filters['conditions'], function ($brands) {
			$this->assertSame($brands['new'], 2);
			$this->assertSame($brands['used'], 1);
			$this->assertSame($brands['refurbished'], 1);
		});
	}

	public function test_it_parses_the_features_filters_for_a_given_collection()
	{
		$products = collect([
			factory(Product::class)->make(['features' => '{"color": "olive", "weight": "115 Mg", "dimensions": "2 X 19 X 22 inch"}']),
			factory(Product::class)->make(['features' => '{"color": "olive", "weight": "115 Mg", "dimensions": "2 X 19 X 22 inch"}']),
			factory(Product::class)->make(['features' => '{"color": "red", "weight": "116 Mg", "dimensions": "3 X 20 X 23 inch"}']),
			factory(Product::class)->make(['features' => '{"color": "blue", "weight": "117 Mg", "dimensions": "4 X 21 X 24 inch"}']),

			//this product should not appear in results because these keys are not filterable.
			factory(Product::class)->make(['features' => '{"foo": "111", "bar": "222", "biz": "333"}']),
		]);

		factory('Antvel\Features\Models\Feature')->states('filterable')->create(['name' => 'color']);
		factory('Antvel\Features\Models\Feature')->states('filterable')->create(['name' => 'weight']);
		factory('Antvel\Features\Models\Feature')->states('filterable')->create(['name' => 'dimensions']);

		$filters = Filters::parse($products);
		$filtersKeys = array_keys($filters);

		//asserting the not filterable features not appear in results
		$this->assertFalse(in_array('foo', $filtersKeys));
		$this->assertFalse(in_array('bar', $filtersKeys));
		$this->assertFalse(in_array('biz', $filtersKeys));

		//asserting we received the correct results.
		$this->assertTrue(is_array($filters));
		$this->assertTrue(count($filters) > 0);
		$this->assertArrayHasKey('color', $filters);
		$this->assertArrayHasKey('weight', $filters);
		$this->assertArrayHasKey('dimensions', $filters);

		$this->assertSame($filters['color']['olive'], 2);
		$this->assertSame($filters['color']['red'], 1);
		$this->assertSame($filters['color']['blue'], 1);

		$this->assertSame($filters['weight']['115 Mg'], 2);
		$this->assertSame($filters['weight']['116 Mg'], 1);
		$this->assertSame($filters['weight']['117 Mg'], 1);

		$this->assertSame($filters['dimensions']['2 X 19 X 22 inch'], 2);
		$this->assertSame($filters['dimensions']['3 X 20 X 23 inch'], 1);
		$this->assertSame($filters['dimensions']['4 X 21 X 24 inch'], 1);
	}
}
