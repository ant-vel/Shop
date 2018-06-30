<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Notifications;

use Antvel\Tests\TestCase;
use Antvel\Users\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Antvel\Users\Repositories\PusNotificationsRepository;

class PusNotificationsRepositoryTest extends TestCase
{
	protected function data($overrides = [])
	{
		return array_merge([
			'source_path' => 'antvel',
			'source_id' => '1',
			'status' => 'open',
			'label' => 'foo',
		], $overrides);
	}

	/** @test */
	function it_can_list_the_unread_signed_user_notifications()
	{
		$user = factory(User::class)->create();
		$this->actingAs($user);

		factory(DatabaseNotification::class)->states('read')->create([
			'notifiable_id' => $user->id,
			'data' => $this->data(),
		]);

		factory(DatabaseNotification::class)->states('unread')->create([
			'data' => $this->data(['status' => 'new', 'source_id' => '2', 'label' => 'bar']),
			'notifiable_id' => $user->id,
		]);

		$notifications = (new PusNotificationsRepository)->unread();

		$this->assertCount(1, $notifications);
		$this->assertTrue($notifications->last()->get('label') != '');
		$this->assertFalse($notifications->last()->get('hasBeenRead'));
	}

	/** @test */
	function it_can_list_the_read_signed_user_notifications()
	{
		$user = factory(User::class)->create();
		$this->actingAs($user);

		factory(DatabaseNotification::class)->states('read')->create([
			'notifiable_id' => $user->id,
			'data' => $this->data(),
		]);

		factory(DatabaseNotification::class)->states('unread')->create([
			'notifiable_id' => $user->id,
		]);

		$notifications = (new PusNotificationsRepository)->read();

		$this->assertCount(1, $notifications);
		$this->assertTrue($notifications->first()->get('label') != '');
		$this->assertTrue($notifications->first()->get('hasBeenRead'));
	}

	/** @test */
	function it_can_list_all_the_signed_user_notifications()
	{
		$user = factory(User::class)->create();
		$this->actingAs($user);

		$read = factory(DatabaseNotification::class)->states('read')->create([
			'notifiable_id' => $user->id,
			'data' => $this->data(),
		]);

		$unread = factory(DatabaseNotification::class)->states('unread')->create([
			'notifiable_id' => $user->id,
			'data' => $this->data(['status' => 'new', 'source_id' => '2'])
		]);

		$notifications = (new PusNotificationsRepository)->all();

		tap($notifications->where('id', $read->id)->first(), function ($read) {
			$this->assertNotNull($read);
			$this->assertTrue($read->get('label') != '');
			$this->assertTrue($read->get('hasBeenRead'));
		});

		tap($notifications->where('id', $unread->id)->first(), function ($unread) {
			$this->assertNotNull($unread);
			$this->assertTrue($unread->get('label') != '');
			$this->assertFalse($unread->get('hasBeenRead'));
		});
	}
}
