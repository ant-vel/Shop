<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Users\Listeners;

use Carbon\Carbon;
use Antvel\Tests\TestCase;
use Antvel\Users\Models\User;
use Illuminate\Support\Facades\Storage;
use Antvel\Users\Listeners\UpdateProfile;
use Antvel\Users\Events\ProfileWasUpdated;
use Antvel\Users\Models\EmailChangePetition;

class UpdateProfileTest extends TestCase
{
	/** @test */
	function a_given_user_can_update_his_profile()
	{
		$user = factory(User::class)->create(['phone_number' => '112233445566', 'nickname' => 'foo']);

		$this->actingAs($user);

		$event = new ProfileWasUpdated(collect([
			'phone_number' => '002233887711',
			'nickname' => 'gocanto',
		]));

		$this->app->make(UpdateProfile::class)->handle($event);

		tap($user->fresh(), function ($user) {
			$this->assertEquals('002233887711', $user->phone_number);
			$this->assertEquals('gocanto', $user->nickname);
		});
	}

	/** @test */
	function a_given_user_might_want_to_change_his_email_address()
	{
		$user = factory(User::class)->create(['email' => 'foo@bar.com', 'phone_number' => '112233445566', 'nickname' => 'foo']);

		$this->actingAs($user);

		$event = new ProfileWasUpdated(collect([
			'email' => 'new@email.com',
		]));

		$this->app->make(UpdateProfile::class)->handle($event);

		$this->assertEquals('foo@bar.com', $user->fresh()->email);

		tap($user->emailChangePetitions->last(), function ($petition) {
			$this->assertInstanceOf(EmailChangePetition::class, $petition);
			$this->assertEquals('new@email.com', $petition->new_email);
			$this->assertEquals('foo@bar.com', $petition->old_email);
			$this->assertNull($petition->confirmed_at);
			$this->assertFalse($petition->confirmed);
		});
	}

	/** @test */
	function a_given_user_can_update_his_profile_picture()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user);

		$event = new ProfileWasUpdated(collect([
			'pictures' => [
				'storing' => [
					$this->uploadFile($disk = 'images/avatars'),
				]
			]
		]));

		$this->app->make(UpdateProfile::class)->handle($event);

		tap($user->fresh(),  function($user) use ($disk) {
			Storage::disk($disk)->assertExists($this->image($user->image));
			$this->assertNotNull($user->image);
		});
	}

	/** @test */
	function a_given_user_can_update_his_password()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user);

		$event = new ProfileWasUpdated(collect([
			'password' => '654321'
		]));

		$this->app->make(UpdateProfile::class)->handle($event);

		tap($user->fresh(),  function($user) {
			$this->assertTrue($this->app->make('hash')->check('654321', $user->password));
		});
	}
}
