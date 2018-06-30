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
use Antvel\Features\Repositories\FeaturesRepository;

class FeaturesRepositoryTest extends TestCase
{
	/** @test */
	function exposes_the_filterable_features_that_are_used_in_products_listing()
	{
		$notAllowed = factory(Feature::class)->create(['name' => 'bar']);
		$allowed = factory(Feature::class)->states('filterable')->create(['name' => 'foo']);

	    $features = (new FeaturesRepository)->filterable();

	    $this->assertCount(1, $features);

	    tap($features->pluck('name'), function($names) {
	    	$this->assertTrue($names->contains('foo'));
	    	$this->assertFalse($names->contains('bar'));
	    });
	}

	/** @test */
	function generates_an_array_with_the_validation_rules_for_the_filterable_features()
	{
		factory(Feature::class)->states('filterable')->create([
			'name' => 'one',
			'validation_rules' => 'required|max:20|min:10',
		]);

		factory(Feature::class)->states('filterable')->create([
			'name' => 'two',
			'validation_rules' => 'required|min:10',
		]);

		factory(Feature::class)->states('filterable')->create([
			'name' => 'three',
			'validation_rules' => 'required',
		]);

		tap((new FeaturesRepository)->filterableValidationRules(), function($rules) {
			$this->assertCount(3, $rules);
			$this->assertTrue(is_array($rules));
			$this->assertEquals($rules['features.one'], 'required|max:20|min:10');
			$this->assertEquals($rules['features.two'], 'required|min:10');
			$this->assertEquals($rules['features.three'], 'required');
		});
	}
}
