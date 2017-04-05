<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Features\Users;

use Antvel\User\Models\Business;
use Illuminate\Contracts\Auth\Authenticatable;

class BusinessesTest extends UsersTestCase
{
	public function test_create_a_new_business_in_databse()
	{
		$business = factory(Business::class)->create([
    		'user_id' => $this->user(['role' => 'business'])->id
    	])->first();

		$this->assertEquals($business->user->role, 'business');
    	$this->assertInstanceOf(Authenticatable::class, $business->user);
	}
}
