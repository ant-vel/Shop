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

use Carbon\Carbon;
use Antvel\User\Listeners\UpdateProfile;
use Antvel\User\Events\ProfileWasUpdated;
use Antvel\User\Models\EmailChangePetition;

class UpdateProfileTest extends UsersTestCase
{
	public function test_a_given_user_can_update_his_profile()
	{
		$business = $this->business();

		$event = new ProfileWasUpdated([
			'nickname' => 'gocanto'
		], $business->user);

		$listener = (new UpdateProfile(
			$this->getRepo(),
			$this->getPetitionRepo()
		))->handle($event);

		$updatedBusiness = $this->getRepo()->profile($business->user_id);

		$this->assertEquals($updatedBusiness->email, $business->user->email);
		$this->assertEquals($updatedBusiness->nickname, 'gocanto');
	}

	public function test_a_given_user_might_want_to_change_his_email_address()
	{
		$business = $this->business();

		$event = new ProfileWasUpdated([
			'email' => 'gocanto@antvel.com'
		], $business->user);

		$listener = (new UpdateProfile(
			$this->getRepo(),
			$this->getPetitionRepo()
		))->handle($event);

		$petition = $this->getPetitionRepo()->findBy([
			'old_email' => $business->user->email,
			'new_email' => 'gocanto@antvel.com',
			'confirmed' => '0'
		])->first();

		$updatedBusiness = $this->getRepo()->profile($business->user_id);

		$this->assertTrue($petition->expires_at->gt(Carbon::now()));
		$this->assertTrue('gocanto@antvel.com' != $updatedBusiness->email);
		$this->assertInstanceOf(EmailChangePetition::class, $petition);
		$this->assertNull($petition->confirmed_at);
	}
}
