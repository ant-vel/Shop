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
use Antvel\Notifications\Parsers\{ Label, Templates };

class LabelsTest extends TestCase
{
	/**
	 * @test
	 * @expectedException Antvel\Notifications\Parsers\NotificationLabelsException
	 */
	function it_throws_an_exception_if_status_was_not_provided()
	{
		$user = factory(User::class)->create();

		factory(DatabaseNotification::class)->states('read')->create([
			'notifiable_id' => $user->id,
			'data' => ['source_id' => '1']
		]);

		$notification = $user->notifications->first();

		$this->assertEquals(
			Label::make('push.orders')->with($notification->data)->print(),
			'There is no template label for the given source [push.orders] and status code [foo].'
		);
	}

	/**
	 * @test
	 * @expectedException Antvel\Notifications\Parsers\NotificationLabelsException
	 */
	function it_throws_an_exception_if_source_id_was_not_provided()
	{
		$user = factory(User::class)->create();

		factory(DatabaseNotification::class)->states('read')->create([
			'notifiable_id' => $user->id,
			'data' => ['status' => 'open']
		]);

		$notification = $user->notifications->first();

		$this->assertEquals(
			Label::make('push.orders')->with($notification->data)->print(),
			"The order #{$notification->data['source_id']} has been placed."
		);
	}

	/** @test */
	function it_returns_a_default_label_if_a_resource_was_not_found()
	{
		$user = factory(User::class)->create();

		factory(DatabaseNotification::class)->states('read')->create([
			'notifiable_id' => $user->id,
			'data' => ['bar' => 'biz', 'status' => 'foo']
		]);

		$notification = $user->notifications->first();

		$this->assertEquals(
			Label::make('push.orders')->with($notification->data)->print(),
			'There is no template label for the given source [push.orders] and status code [foo].'
		);
	}

	/** @test */
	function it_parses_the_right_label_for_a_given_notification()
	{
		$user = factory(User::class)->create();

		factory(DatabaseNotification::class)->states('read')->create([
			'notifiable_id' => $user->id,
			'data' => ['status' => 'open', 'source_id' => '1']
		]);

		tap($user->notifications->first()->data, function ($data) {
			$this->assertEquals(
				Label::make('push.orders')->with($data)->print(),
				str_replace('source_id', $data['source_id'], Templates::make('push.orders')->get('open'))
			);
		});
	}

	/** @test */
	function it_resolves_the_label_data_from_the_given_array()
	{
		$notification = factory(DatabaseNotification::class)->states('read')->create([
			'data' => ['status' => 'open', 'source_id' => '1']
		]);

		$data = Label::make('push.orders')->with($notification->data)->getData();

		$this->assertSame('open', $data['status']);
		$this->assertEquals(1, $data['source_id']);
	}

	/** @test */
	function it_resolves_the_label_data_from_the_given_collection()
	{
		$notification = factory(DatabaseNotification::class)->states('read')->create([
			'data' => ['status' => 'open', 'source_id' => '1']
		]);

		$data = Label::make('push.orders')->with(collect($notification->data))->getData();

		$this->assertSame('open', $data['status']);
		$this->assertEquals(1, $data['source_id']);
	}

	/** @test */
	function it_resolves_the_label_data_from_a_given_eloquent_instance()
	{
		$order = factory('Antvel\Orders\Models\Order')->create(['status' => 'open']);

		$data = Label::make('push.orders')->with($order)->getData();

		$this->assertSame('open', $data['status']);
		$this->assertEquals(1, $data['source_id']);
	}
}
