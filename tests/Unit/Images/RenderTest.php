<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Images;

use Antvel\Tests\TestCase;
use Antvel\Support\Images\Render;

class RenderTest extends TestCase
{
	/**
	 * @test
     */
	function it_renders_a_given_picture()
	{
		$image = Render::image($file = 'stub.jpg')->mock();

		$this->assertEquals(
			realpath($image),
			$this->basePath() . $file
		);
	}

	/** @test */
	function it_returns_a_default_path_if_the_given_picture_was_not_found()
	{
	    $image = Render::image($file = 'foo.jpg')->mock();

	    $this->assertEquals(
			realpath($image),
			$this->basePath() . 'no-image.jpg'
		);
	}

	/** @test */
	function it_creates_a_thumbnail_for_the_given_width_option()
	{
	    $image = Render::image($file = 'stub.jpg', ['w' => '10'])->mock();

	    $this->assertEquals(
			realpath($image),
			$thumbnail = $this->basePath() . 'stub_w10.jpg'
		);

		$this->assertTrue(file_exists($thumbnail));

	    @unlink($thumbnail);

	    $this->assertFalse(file_exists($thumbnail));
	}

	/** @test */
	function it_creates_a_thumbnail_for_the_given_height_option()
	{
	    $image = Render::image($file = 'stub.jpg', ['h' => '10'])->mock();

		$this->assertEquals(
			realpath($image),
			$thumbnail = $this->basePath() . 'stub_h10.jpg'
		);

		$this->assertTrue(file_exists($thumbnail));

	    @unlink($thumbnail);

	    $this->assertFalse(file_exists($thumbnail));
	}

	/** @test */
	function it_creates_a_thumbnail_for_the_given_options()
	{
	    $image = Render::image($file = 'stub.jpg', ['w' => 10, 'h' => '10'])->mock();

		$this->assertEquals(
			realpath($image),
			$thumbnail = $this->basePath() . 'stub_w10_h10.jpg'
		);

		$this->assertTrue(file_exists($thumbnail));

	    @unlink($thumbnail);

	    $this->assertFalse(file_exists($thumbnail));
	}

	protected function basePath()
	{
		return realpath(storage_path()) . '/images/';
	}
}
