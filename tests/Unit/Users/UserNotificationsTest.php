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

use Antvel\Tests\TestCase;
use Antvel\Users\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class UserNotificationsTest extends TestCase
{
	/** @test */
	function an_authorized_user_can_mark_a_given_notification_as_read()
	{
		$user = factory(User::class)->create();

		$notificationA = factory(DatabaseNotification::class)->states('unread')->create(['notifiable_id' => $user->id]);
		$notificationB = factory(DatabaseNotification::class)->states('unread')->create(['notifiable_id' => $user->id]);

		$user->markNotificationAsRead($notificationA->id);

	    $this->assertNotNull($notificationA->fresh()->read_at);
	    $this->assertNull($notificationB->fresh()->read_at);
	}

	function a_given_user_must_not_mark_other_users_notifications_as_read()
	{
		$user = factory(User::class)->create();
		$anotherUser = factory(User::class)->create();

		$this->actingAs($user);

		$notificationA = factory(DatabaseNotification::class)->states('unread')->create(['notifiable_id' => $anotherUser->id]);
		$notificationB = factory(DatabaseNotification::class)->states('unread')->create(['notifiable_id' => $anotherUser->id]);

		$user->markNotificationAsRead($notificationA->id);

	    $this->assertNull($notificationA->fresh()->read_at);
	    $this->assertNull($notificationB->fresh()->read_at);
	}
}
