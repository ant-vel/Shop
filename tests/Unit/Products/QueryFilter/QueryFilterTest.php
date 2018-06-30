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
use Illuminate\Support\Collection;
use Antvel\Product\Models\QueryFilter;

class QueryFilterTest extends TestCase
{
	/** @test */
	function it_parses_the_request_against_the_allowed_filters()
	{
		$query = new QueryFilter([
			'search' => 'search value',
	        'category' => '1',
	        'conditions' => 'conditions value',
	        'brands' => 'brands value',
			'min' => '1',
	        'max' => '5',
	        'inactives' => 'inactives value',
			'low_stock' => 'low_stock value',
			'not_allowed' => 'not allowed value'
		]);

		tap(array_keys($query->getRequest()), function ($request) {
			$this->assertCount(8, $request);
			$this->assertTrue(in_array('search', $request));
			$this->assertTrue(in_array('category', $request));
			$this->assertTrue(in_array('conditions', $request));
			$this->assertTrue(in_array('brands', $request));
			$this->assertTrue(in_array('min', $request));
			$this->assertTrue(in_array('max', $request));
			$this->assertTrue(in_array('inactives', $request));
			$this->assertTrue(in_array('low_stock', $request));
			$this->assertFalse(in_array('not_allowed', $request));
		});
	}

	/** @test */
	function check_whether_the_query_has_any_requested_data()
	{
		$query = new QueryFilter(['search' => 'search value']);

		$this->assertTrue($query->hasRequest());
	}

	/** @test */
	function it_is_able_to_parse_the_filterable_features_to_be_used_for_a_given_request()
	{
		factory('Antvel\Features\Models\Feature')->states('filterable')->create(['name' => 'weight']);

		$query = new QueryFilter(['color' => 'red', 'weight' => '11']);

		tap($query->getRequest(), function ($request) {
			$this->assertTrue(isset($request['features']));
			$this->assertTrue(isset($request['features']['weight']));
			$this->assertEquals('11', $request['features']['weight']);
			$this->assertFalse(isset($request['features']['color']));
		});
	}
}
