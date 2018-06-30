<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Categories;

use Antvel\Tests\TestCase;
use Antvel\Categories\Normalizer;
use Antvel\Product\Models\Product;
use Antvel\Categories\Models\Category;
use Antvel\Categories\Repositories\CategoriesRepository;

class CategoriesRepositoryTest extends TestCase
{
	/** @test */
	function it_can_list_categories_that_have_products_associated_with()
	{
		factory(Category::class)->create(['name' => 'categoryA']);
		$categoryB = factory(Category::class)->create(['name' => 'categoryB']);
		$categoryC = factory(Category::class)->create(['name' => 'categoryC']);

		factory(Product::class)->create(['name' => 'productB', 'category_id' => $categoryB->id]);
		factory(Product::class)->create(['name' => 'productC', 'category_id' => $categoryC->id]);

		$categories = (new CategoriesRepository)->categoriesWithProducts()->pluck('name');

		$this->assertCount(2, $categories);
		$this->assertTrue($categories->contains('categoryB'));
		$this->assertTrue($categories->contains('categoryC'));
		$this->assertFalse($categories->contains('categoryA'));
	}

	/** @test */
	function it_can_filter_a_given_list_categories_that_have_products_associated_with()
	{
		factory(Category::class)->create(['name' => 'accessories', 'description' => 'foo']);
		$categoryB = factory(Category::class)->create(['name' => 'sports', 'description' => 'bar']);
		$categoryC = factory(Category::class)->create(['name' => 'services', 'description' => 'biz']);

		factory(Product::class)->create(['name' => 'productB', 'category_id' => $categoryB->id]);
		factory(Product::class)->create(['name' => 'productC', 'category_id' => $categoryC->id]);

		$categories = (new CategoriesRepository)->categoriesWithProducts([
			'name' => 'acc',
			'description' => 'ar',
		])->pluck('name');

		$this->assertCount(1, $categories);
		$this->assertTrue($categories->contains('sports'));
		$this->assertFalse($categories->contains('services'));
		$this->assertFalse($categories->contains('accessories'));
	}

	/** @test */
	function it_can_list_a_given_category_children()
	{
		$categoryA = factory(Category::class)->create(['id' => 1, 'name' => 'A', 'category_id' => null]);
		$categoryB = factory(Category::class)->create(['id' => 2, 'name' => 'B', 'category_id' => $categoryA->id]);
		$categoryC = factory(Category::class)->create(['id' => 3, 'name' => 'C', 'category_id' => $categoryA->id]);
		$categoryD = factory(Category::class)->create(['id' => 4, 'name' => 'D', 'category_id' => $categoryB->id]);
		$categoryE = factory(Category::class)->create(['id' => 5, 'name' => 'E', 'category_id' => $categoryD->id]);
		$categoryF = factory(Category::class)->create(['id' => 6, 'name' => 'F', 'category_id' => $categoryC->id]);

		$children = (new CategoriesRepository)->childrenOf($categoryA->id);

		$this->assertCount(5, Normalizer::generation($children));
	}
}
