<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\UsersNotifications;

use Antvel\Tests\TestCase;
use Antvel\Users\Models\User;
use Antvel\Notifications\Parsers\Label;
use Antvel\Users\Notifications\OrderWasUpdated;

class OrderWasUpdatedTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();

		$this->routes = [ //while refactoring Antvel orders component.
			'seller' => 'orders.show_seller_order',
			'customer' => 'orders.show_order',
		];

		$this->app->make('router')->get('seller/{orderId}', [
			'uses' => function() {}, 'as' => $this->routes['seller']
		]);

		$this->app->make('router')->get('customer/{orderId}', [
			'uses' => function() {}, 'as' => $this->routes['customer']
		]);
	}

	/** @test */
	function an_owner_notification_might_contain_the_right_information()
	{
		$seller = factory(User::class)->states('seller')->create();
		$customer = factory(User::class)->states('customer')->create();
		$order = factory('Antvel\Orders\Models\Order')->create(['user_id' => $customer->id, 'seller_id' => $seller->id, 'status' => 'open']);

		$order->owner->notify(new OrderWasUpdated($order));

		tap($order->owner->notifications->first(), function ($notification) use ($order) {
			$this->assertEquals($order->owner->id, $notification->notifiable_id);
			$this->assertEquals(OrderWasUpdated::class, $notification->type);
			$this->assertEquals(User::class, $notification->notifiable_type);
			$this->assertNull($notification->read_at);

			$this->assertEquals($notification->data['label'], Label::make('push.orders')->with($notification->data)->print());
			$this->assertEquals(route($this->routes['customer'], $order), $notification->data['source_path']);
			$this->assertEquals($order->id, $notification->data['source_id']);
			$this->assertEquals('open', $notification->data['status']);
		});
	}

	/** @test */
	function a_seller_notification_might_contain_the_right_information()
	{
		$seller = factory(User::class)->states('seller')->create();
		$owner = factory(User::class)->make(['id' => 2]);
		$order = factory('Antvel\Orders\Models\Order')->make(['id' => 1, 'user_id' => $owner->id, 'seller_id' => $seller->id]);

		$order->seller->notify(new OrderWasUpdated($order));

		tap($order->seller->notifications->first(), function ($notification) use ($order) {
			$this->assertEquals($order->seller->id, $notification->notifiable_id);
			$this->assertEquals(OrderWasUpdated::class, $notification->type);
			$this->assertEquals(User::class, $notification->notifiable_type);
			$this->assertNull($notification->read_at);

			$this->assertEquals($notification->data['label'], Label::make('push.orders')->with($notification->data)->print());
			$this->assertEquals(route($this->routes['seller'], $order), $notification->data['source_path']);
			$this->assertEquals($order->status, $notification->data['status']);
			$this->assertEquals($order->id, $notification->data['source_id']);
		});
	}
}
