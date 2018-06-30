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
use Antvel\Features\Models\Feature;

class FeaturesTest extends TestCase
{
	/** @test */
	function it_can_create_a_new_feature_with_default_values()
	{
		$feature = Feature::create(['name' => 'feature']);

		$this->assertTrue($feature->exists());
		$this->assertEquals('feature', $feature->name);
	}

	/** @test */
	function it_can_create_a_new_required_feature()
	{
		$feature = Feature::create([
			'name' => 'feature',
			'input_type' => 'text',
			'product_type' => 'item',
			'help_message' => 'Tooltip message',
			'status' => 1,
			'validation_rules' => [
				'required' => 1
			]
		]);

		$this->assertEquals('feature', $feature->name);
		$this->assertEquals('text', $feature->input_type);
		$this->assertEquals('item', $feature->product_type);
		$this->assertEquals('Tooltip message', $feature->help_message);
		$this->assertTrue(!! $feature->status);
		$this->assertEquals('required', $feature->validation_rules);
	}

	/** @test */
	function it_can_update_a_given_feature()
	{
		$feature = factory(Feature::class)->create()->first();

		$feature->update([
			'name' => 'feature',
			'input_type' => 'text',
			'product_type' => 'item',
			'help_message' => 'Tooltip message',
			'status' => 1
		]);

		$this->assertEquals('feature', $feature->name);
		$this->assertEquals('text', $feature->input_type);
		$this->assertEquals('item', $feature->product_type);
		$this->assertEquals('Tooltip message', $feature->help_message);
		$this->assertTrue((bool) $feature->status);
	}

	/** @test */
	function it_can_update_a_given_required_feature_and_mark_it_as_no_required()
	{
		$feature = factory(Feature::class)->create(['validation_rules' => 'required']);

		$feature->update(['name' => 'foo']);

		$this->assertEquals('foo', $feature->fresh()->name);
		$this->assertNotNull($feature->fresh()->validation_rules);
	}
}
