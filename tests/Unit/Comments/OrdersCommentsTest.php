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
use Antvel\Orders\Models\Order;

class OrdersCommentsTest extends TestCase
{
	/** @test */
	function a_given_order_can_be_commented()
	{
		$order = factory(Order::class)->make(['id' => 1]);
		$comments = $order->comments()->create(['data' => ['message' => 'foo']]);

		tap($comments->first(), function ($comment) use ($order) {
			$this->assertEquals(Order::class, $comment->commentable_type);
			$this->assertEquals($order->id, $comment->commentable_id);
			$this->assertEquals('foo', $comment->data['message']);
			$this->assertNull($comment->read_at);
		});
	}

	/** @test */
	function comments_from_a_given_order_can_be_listed()
	{
		$order = factory(Order::class)->create();
		$order->comments()->create(['data' => ['message' => 'foo']]);
		$order->comments()->create(['data' => ['message' => 'bar']]);
		$order->comments()->create(['data' => ['message' => 'biz']]);

		$this->assertCount(3, $order->comments);
		$order->comments->each(function ($comment) use ($order) {
			$this->assertEquals(Order::class, $comment->commentable_type);
			$this->assertEquals($order->id, $comment->commentable_id);
		});

	}
}
