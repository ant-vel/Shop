<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Products\QueryFilter;

use Antvel\Tests\TestCase;
use Antvel\Product\Models\Product;
use Antvel\Categories\Models\Category;

class IntegrationTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();

		$this->repository = $this->app->make('Antvel\Product\Products');
	}

	public function test_it_can_retrieve_products_by_conditions()
	{
		factory(Product::class)->create(['condition' => 'new']);
		factory(Product::class)->create(['condition' => 'used']);
		factory(Product::class)->create(['condition' => 'refurbished']);

		$products = $this->repository->filter(['conditions' => 'new'])->get();

		$this->assertCount(1, $products);
		foreach ($products as $product) {
			$this->assertEquals('new', $product->condition);
		}
	}

	public function test_it_can_retrieve_products_by_brands()
	{
		factory(Product::class)->create(['brand' => 'samsung']);
		factory(Product::class)->create(['brand' => 'apple']);
		factory(Product::class)->create(['brand' => 'lg']);

		$products = $this->repository->filter(['brands' => 'apple'])->get();

		$this->assertCount(1, $products);
		foreach ($products as $product) {
			$this->assertEquals('apple', $product->brand);
		}
	}

	public function test_it_can_retrieve_products_by_searching()
	{
		factory(Product::class)->create(['name' => 'iPhone 7','description' => 'The iPhone 7 description']);
		factory(Product::class)->create(['name' => 'Galaxy S8','description' => 'The Galaxy S8 description']);
		factory(Product::class)->create(['name' => 'LG G6','description' => 'The LG G6 description']);

		$products = $this->repository->filter([
			'search' => 'iPhone',
		])->get();

		$this->assertCount(1, $products);
		$this->assertEquals('iPhone 7', $products->pluck('name')->first());
		$this->assertEquals('The iPhone 7 description', $products->pluck('description')->first());
	}

	public function test_it_can_retrieve_products_by_price()
	{
		factory(Product::class)->create(['price' => 1000]);
		factory(Product::class)->create(['price' => 2000]);
		factory(Product::class)->create(['price' => 3000]);

		$byMin = $this->repository->filter(['min' => 1000])->get();
		$byMax = $this->repository->filter(['max' => 2000])->get();
		$byMaxAndMax = $this->repository->filter(['min' => 2200, 'max' => 3000])->get();

		$this->assertCount(3, $byMin);
		$this->assertCount(2, $byMax);
		$this->assertCount(1, $byMaxAndMax);
	}

	public function test_it_can_retrieve_all_products()
	{
		factory(Product::class, 2)->create();

		$products = $this->repository->filter()->get();

		$this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $products);
		$this->assertCount(2, $products);
	}

	public function test_it_can_retrieve_products_by_categories()
	{
		$categoryA = factory(Category::class)->create(['id' => 1, 'name' => 'A', 'category_id' => null]);
		$categoryB = factory(Category::class)->create(['id' => 2, 'name' => 'B', 'category_id' => $categoryA->id]);
		$categoryC = factory(Category::class)->create(['id' => 3, 'name' => 'C', 'category_id' => $categoryA->id]);
		$categoryD = factory(Category::class)->create(['id' => 4, 'name' => 'D', 'category_id' => $categoryB->id]);
		$categoryE = factory(Category::class)->create(['id' => 5, 'name' => 'E', 'category_id' => $categoryD->id]);
		$categoryF = factory(Category::class)->create(['id' => 6, 'name' => 'F', 'category_id' => $categoryC->id]);

		$productsA = factory(Product::class)->create(['id' => 1, 'category_id' => $categoryA->id]);
		$productsB = factory(Product::class)->create(['id' => 2, 'category_id' => $categoryB->id]);
		$productsC = factory(Product::class)->create(['id' => 3, 'category_id' => $categoryC->id]);
		$productsD = factory(Product::class)->create(['id' => 4, 'category_id' => $categoryD->id]);
		$productsE = factory(Product::class)->create(['id' => 5, 'category_id' => $categoryE->id]);
		$productsF = factory(Product::class)->create(['id' => 6, 'category_id' => $categoryF->id]);

		$results = $this->repository->filter(['category' => $categoryA->id . '|' . $categoryA->name])->get();

		$this->assertCount(6, $results);
		$this->assertSame($productsA->category_id, $categoryA->id);
		$this->assertSame($productsB->category_id, $categoryB->id);
		$this->assertSame($productsC->category_id, $categoryC->id);
		$this->assertSame($productsD->category_id, $categoryD->id);
		$this->assertSame($productsE->category_id, $categoryE->id);
		$this->assertSame($productsF->category_id, $categoryF->id);
	}

	public function test_it_can_retrieve_products_by_categories_and_its_children()
	{
		$tools = factory(Category::class)->create(['name' => 'tools']);
		$screes = factory(Category::class)->create(['category_id' => $tools->id]);
		$other = factory(Category::class)->create(['name' => 'other']);

		$toolsProducts = factory(Product::class, 2)->create(['category_id' => $tools->id]);
		$softwareProducts = factory(Product::class, 2)->create(['category_id' => $screes->id]);
		$otherProducts = factory(Product::class, 2)->create(['category_id' => $other->id]);

		$byToolsAndChildren = $this->repository->filter([
			'category' => $tools->id . '|' . $tools->name,
		])->get();

		$this->assertCount(4, $byToolsAndChildren);
		$byToolsAndChildren->each(function ($item) use ($tools, $screes) {
			$this->assertTrue(
				$item->category_id == $tools->id || $item->category_id == $screes->id
			);
		});
	}

	public function test_it_can_retrieve_products_by_a_given_advanced_searching()
	{
		//Categories setup
		$category = factory(Category::class)->create(['name' => 'Entertainment']);
		$subCategories = factory(Category::class, 2)->create(['category_id' => $category->id]);

		$other = factory(Category::class)->create(['name' => 'Other']);
		$otherSubCategories = factory(Category::class, 2)->create(['category_id' => $other->id]);

		//Products setup
		factory(Product::class)->create();

		$list = factory(Product::class)->create([
			'description' => 'Entertainment Product',
			'category_id' => $category->id,
			'condition' => 'new',
			'brand' => 'LG',
		]);

		$products = $this->repository->filter([
			'category' => $category->id .'|'. $category->name,
			'min' => $list->pluck('price')->min(),
			'max' => $list->pluck('price')->max(),
			'search' => 'Entertainment',
			'condition' => 'new',
			'brands' => 'LG',
		])->get();

		$this->assertCount(1, $products);
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function it_filters_products_by_a_given_color_and_weight()
	{
		$this->usingMySql();

		factory('Antvel\Features\Models\Feature')->states('filterable')->create(['name' => 'color']);
		factory('Antvel\Features\Models\Feature')->states('filterable')->create(['name' => 'weight']);

	    factory(Product::class)->create(['name' => 'per', 'features' => '{"color": "red", "weight": "11"}']);
	    factory(Product::class)->create(['name' => 'foo', 'features' => '{"color": "red", "weight": "11"}']);
	    factory(Product::class)->create(['name' => 'tar', 'features' => '{"color": "blue", "weight": "10"}']);
	    factory(Product::class)->create(['name' => 'biz', 'features' => '{"color": "green", "weight": "12"}']);
	    factory(Product::class)->create(['name' => 'bar', 'features' => '{"color": "yellow", "weight": "13"}']);

		$products = $this->repository->filter([
			'color' => 'red',
			'weight' => '11',
		]);

		tap($products->get()->pluck('features'), function ($products) {
			$this->assertCount(2, $products);
			foreach ($products as $product) {
				$this->assertEquals('red', $product['color']);
				$this->assertEquals('11', $product['weight']);
			}
		});

	    $this->artisan('migrate:reset', ['--database' => self::TESTING_DB]);
	}
}
