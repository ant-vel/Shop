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
use Illuminate\Support\Facades\Storage;
use Antvel\Support\Images\Manager;

class ManagerTest extends TestCase
{
	/** @test */
	function it_can_update_a_string_input()
	{
		$current_picture = 'images/foo.jgp';

		$data = [
			'storing' => [
				$this->persistentUpload($disk = 'images'),
			]
		];

		tap(Manager::parse($data)->on($disk)->update($current_picture), function ($picture) use ($disk) {
			$this->assertTrue(count($picture) > 0);
			Storage::disk($disk)->assertExists($this->image($picture['path']));
		});
	}

	/** @test */
	function it_parses_the_right_data_if_there_was_not_provided_inputs()
	{
		$current_picture= 'images/foo.jgp';

		$data = [
			'storing' => []
		];

		tap(Manager::parse($data)->on('images')->update($current_picture), function ($picture) {
			$this->assertEquals('images/foo.jgp', $picture['path']);
			$this->assertTrue(count($picture) > 0);
			$this->assertNull($picture['id']);
		});
	}

	/** @test */
	function it_can_parse_and_store_an_given_batch_of_images()
	{
		$data = [
			'storing' => [
				$this->uploadFile($disk = 'images'),
				$this->uploadFile($disk),
			]
		];

		$pictures = Manager::parse($data)->on($disk)->store();

		$this->assertCount(2, $pictures);

		foreach ($pictures as $picture) {
			Storage::disk($disk)->assertExists(
				$this->image($picture['path'])
			);
		}
	}

	/** @test */
	function it_can_delete_pictures()
	{
		$current_pictures = $this->makePictures($disk = 'images');

		$data = [
			'deleting' => [
				$current_pictures[0]['id'] => true,
			]
		];

		$pictures = Manager::parse($data);

		$this->assertTrue($pictures->wantsDeletion());

		$pictures = $pictures->delete($current_pictures);

		Storage::disk($disk)->assertExists($this->image($current_pictures[1]['path']));
		Storage::disk($disk)->assertMissing($this->image($current_pictures[0]['path']));
		$this->assertTrue(in_array($current_pictures[0]['id'], $pictures));

		$this->cleanDirectory($disk);
	}

	protected function makePictures($disk)
	{
		return \Illuminate\Support\Collection::make([
			[
				'id' => 1,
				'path' => $this->persistentUpload($disk, 'foo.jpg')->store($disk),
			],
			[
				'id' => 2,
				'path' => $this->persistentUpload($disk, 'foo.jpg')->store($disk),
			]
		]);
	}
}
