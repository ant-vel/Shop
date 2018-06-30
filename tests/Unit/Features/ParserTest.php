<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Features;

use Antvel\Tests\TestCase;
use Antvel\Features\Parser;
use Antvel\Product\Models\Product;

class ParserTest extends TestCase
{
	/** @test */
	function it_returns_a_default_value_if_nothing_was_provided()
	{
		$this->assertNull(Parser::toJson([]));
		$this->assertNull(Parser::toJson(null));
	}

	/** @test */
	function it_trims_falsy_values()
	{
		$parser = Parser::toJson([
			'color' => 'red',
			'weight' => '100',
			'dimensions' => ''
		]);

		$parser = json_decode($parser, true);

		$this->assertCount(2, $parser);
		$this->assertEquals('red', $parser['color']);
		$this->assertEquals('100', $parser['weight']);
		$this->assertTrue(empty($parser['dimensions']));
	}

	/** @test */
	function it_updates_a_feature_key_from_a_given_products_collections()
	{
		$products = collect([
			factory(Product::class)->make(['id' => '1', 'features' => json_encode(['color' => 'red', 'weight' => '10', 'dimensions' => '1x2x3'])]),
			factory(Product::class)->make(['id' => '2', 'features' => json_encode(['color' => 'red', 'weight' => '11', 'dimensions' => '2x3x4'])])
		]);

		foreach ($products as $product) {
			$this->assertTrue(empty($product->features['foo']));
			$this->assertTrue(isset($product->features['color']));
			$this->assertEquals('red', $product->features['color']);
		}

		$data = Parser::replaceTheGivenKeyFor($products, 'color', 'foo');

		foreach ($products as $product) {
			$first = $products->where('id', $product->id)->first();

			$this->assertTrue(empty($data[$product->id]['color']));
			$this->assertEquals($data[$product->id]['foo'], 'red');
			$this->assertEquals($data[$product->id]['weight'], $first->features['weight']);
			$this->assertEquals($data[$product->id]['dimensions'], $first->features['dimensions']);
		}
	}
}
