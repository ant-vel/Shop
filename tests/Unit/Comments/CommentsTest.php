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
use Antvel\Comments\Models\Comment;

class CommentsTest extends TestCase
{
	/** @test */
	function a_comment_can_be_marked_as_read()
	{
		$comment = factory(Comment::class)->create();

		$this->assertNull($comment->read_at);

		$comment->markAsRead();

		$this->assertNotNull($comment->read_at);
	}

	/** @test */
	function it_knows_whether_a_comment_has_been_read()
	{
		$comment = factory(Comment::class)->create();

		$this->assertTrue($comment->unread());
		$this->assertFalse($comment->read());

		$comment->markAsRead();

		$this->assertFalse($comment->unread());
		$this->assertTrue($comment->read());
	}
}
