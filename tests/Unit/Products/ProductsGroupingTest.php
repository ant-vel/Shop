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

use Antvel\Product\Models\Product;

class ProductsGroupingTest extends ProductsTestCase
{
	/** @test */
	function it_can_list_a_given_product_grouping_list()
	{
	 	$productA = factory(Product::class)->create(['name' => 'Product A']);
	    $productB = factory(Product::class)->create(['name' => 'Product B']);
	    $productC = factory(Product::class)->create(['name' => 'Product C']);

		$productA->groupWith($productB, $productC);

	    tap($productA->group, function ($grouping) use ($productB, $productC) {
	    	$this->assertTrue($grouping->first()->is($productB));
	    	$this->assertTrue($grouping->last()->is($productC));
	    	$this->assertCount(2, $grouping);
	    });

	    $this->assertCount(0, $productB->group);
	}

	/** @test */
	function it_group_a_product_with_the_given_ones()
	{
	    $productA = factory(Product::class)->create(['name' => 'Product A']);
	    $productB = factory(Product::class)->create(['name' => 'Product B']);
	    $productC = factory(Product::class)->create(['name' => 'Product C']);

	    $productA->groupWith($productB, $productC);
	    $productB->groupWith($productC);

	    $groupingA = Product::with('group')->where('id', $productA->id)->first();
	    $groupingB = Product::with('group')->where('id', $productB->id)->first();
	    $groupingC = Product::with('group')->where('id', $productC->id)->first();

	    tap($groupingA->group, function ($grouping) use ($productB, $productC) {
	    	$this->assertTrue($grouping->first()->is($productB));
	    	$this->assertTrue($grouping->last()->is($productC));
	    	$this->assertCount(2, $grouping);
	    });

	    tap($groupingB->group, function ($grouping) use ($productC) {
	    	$this->assertTrue($grouping->first()->is($productC));
	    	$this->assertCount(1, $grouping);
	    });

	    $this->assertCount(0, $groupingC->group);
	}

	/** @test */
	function it_group_a_product_with_the_given_products_referring_by_their_ids()
	{
		$productA = factory(Product::class)->create(['name' => 'Product A']);
		$productB = factory(Product::class)->create(['name' => 'Product B']);
	    $productC = factory(Product::class)->create(['name' => 'Product C']);

		$productA->groupWith($productB->id, $productC->id);

		tap($productA->group, function ($grouping) use ($productB, $productC) {
	    	$this->assertTrue($grouping->first()->is($productB));
	    	$this->assertTrue($grouping->last()->is($productC));
	    	$this->assertCount(2, $grouping);
	    });
	}

	/** @test */
	function products_group_have_to_be_constructed_of_unique_products()
	{
		$productA = factory(Product::class)->create(['name' => 'Product A']);
		$productB = factory(Product::class)->create(['name' => 'Product B']);
	    $productC = factory(Product::class)->create(['name' => 'Product C']);

		$productA->groupWith($productB, $productB);
		$productB->groupWith($productC->id, $productC->id);

		tap($productA->group, function ($grouping) use ($productB) {
	    	$this->assertTrue($grouping->first()->is($productB));
	    	$this->assertCount(1, $grouping);
	    });

	    tap($productB->group, function ($grouping) use ($productC) {
	    	$this->assertTrue($grouping->first()->is($productC));
	    	$this->assertCount(1, $grouping);
	    });
	}

	/** @test */
	function it_can_delete_products_from_a_given_group()
	{
		$productA = factory(Product::class)->create(['name' => 'Product A']);
	    $productB = factory(Product::class)->create(['name' => 'Product B']);
	    $productC = factory(Product::class)->create(['name' => 'Product C']);

	    $productA->groupWith($productB, $productC);
	    $productB->groupWith($productC);

	    $productB->ungroup($productC);

	    tap($productA->group, function ($grouping) use ($productB, $productC) {
	    	$this->assertTrue($grouping->first()->is($productB));
	    	$this->assertTrue($grouping->last()->is($productC));
	    	$this->assertCount(2, $grouping);
	    });

	    $this->assertCount(0, $productB->group);
	}

	/** @test */
	function it_can_delete_products_from_a_given_group_referring_by_their_ids()
	{
		$productA = factory(Product::class)->create(['name' => 'Product A']);
	    $productB = factory(Product::class)->create(['name' => 'Product B']);
	    $productC = factory(Product::class)->create(['name' => 'Product C']);

	    $productA->groupWith($productB->id, $productC->id);
	    $productB->groupWith($productC->id);

	    $productB->ungroup($productC->id);

	    tap($productA->group, function ($grouping) use ($productB, $productC) {
	    	$this->assertTrue($grouping->first()->is($productB));
	    	$this->assertTrue($grouping->last()->is($productC));
	    	$this->assertCount(2, $grouping);
	    });

	    $this->assertCount(0, $productB->group);
	}

	/** @test */
	function it_is_able_to_retrieve_parents_from_a_given_product()
	{
		$productA = factory(Product::class)->create(['name' => 'Product A']);
	    $productB = factory(Product::class)->create(['name' => 'Product B']);
	    $productC = factory(Product::class)->create(['name' => 'Product C']);

	    $productA->groupWith($productB, $productC);
	    $productB->groupWith($productC);

	    tap($productB->groupParent, function ($parents) use ($productA) {
	    	$this->assertCount(1, $parents);
	    	$this->assertTrue($parents->first()->is($productA));
	    });

	    tap($productC->groupParent, function ($parents) use ($productA, $productB) {
	    	$this->assertCount(2, $parents);
	    	$this->assertTrue($parents->first()->is($productA));
	    	$this->assertTrue($parents->last()->is($productB));
	    });
	}
}
