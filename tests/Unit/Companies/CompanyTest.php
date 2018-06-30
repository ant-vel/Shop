<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Companies;

use Antvel\Tests\TestCase;
use Antvel\Companies\Models\Company;

class CompanyTest extends TestCase
{
	/** @test */
	function a_company_has_many_locations()
	{
		$default = factory(Company::class)->states('default')->create();
		$locationA = factory(Company::class)->create(['company_id' => $default]);
		$locationB = factory(Company::class)->create(['company_id' => $default]);

		$this->assertCount(2, $default->locations);
	}

	/** @test */
	function a_company_can_add_a_new_location_to_its_locations_list()
	{
		$location = factory(Company::class)->make();
		$default = factory(Company::class)->states('default')->create();


		$default->locations()->save($location);

		$this->assertCount(1, $default->locations);
		tap($default->locations->last(), function ($last) use ($location) {
			$this->assertTrue($last->is($location));
		});
	}
}
