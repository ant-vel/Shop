<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Users;

use Carbon\Carbon;
use Antvel\Tests\TestCase;
use Antvel\Users\Models\User;
use Antvel\Users\Models\EmailChangePetition;
use Illuminate\Contracts\Auth\Authenticatable;

class UserEmailChangePetitionTest extends TestCase
{
	/** @test */
	function it_is_able_to_make_email_petitions()
	{
		$user = factory(User::class)->create();

		$petition = $user->makePetitionFor('gocanto@bar.com');

		$petition->load('user');

		$this->assertInstanceOf(EmailChangePetition::class, $petition);
		$this->assertInstanceOf(Authenticatable::class, $petition->user);
		$this->assertEquals($petition->new_email, 'gocanto@bar.com');
		$this->assertTrue($petition->expires_at->gt(Carbon::now()));
		$this->assertEquals($petition->old_email, $user->email);
		$this->assertTrue($petition->user->is($user));
	}

	/** @test */
	function it_is_able_to_refresh_email_petitions()
	{
		$petition = factory(EmailChangePetition::class)->create([
			'expires_at' => Carbon::now()->subDays(5),
			'new_email' => 'foo@bar.com',
		]);

		$petition->user->makePetitionFor('foo@bar.com');

		tap($petition->fresh(), function ($petition) {
			$this->assertInstanceOf(EmailChangePetition::class, $petition);
			$this->assertInstanceOf(Authenticatable::class, $petition->user);
			$this->assertTrue($petition->expires_at->gt(Carbon::now()));
			$this->assertCount(1, EmailChangePetition::get());
			$this->assertFalse($petition->confirmed);
		});
	}

	/** @test */
	function it_is_able_to_confirm_email_petitions()
	{
		$petition = factory(EmailChangePetition::class)->create([
			'token' => 'valid_token', 'new_email' => 'foo@bar.com'
		]);

		$confirmedPetition = $petition->user->confirmPetition(
			'valid_token', 'foo@bar.com'
		);

		$this->assertNotNull($petition->fresh()->confirmed_at);
		$this->assertTrue($petition->fresh()->confirmed);
	}
}
