<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit;

use Antvel\Tests\TestCase;
use Antvel\Support\Pictures;
use Illuminate\Support\Facades\Storage;

class PituresTest extends TestCase
{
	protected function pictures($attributes = [])
	{
		return Pictures::prepare($attributes);
	}

	/**
	 * Returns a uploaded file name.
	 *
	 * @param  string $fileName
	 * @return string
	 */
	protected function image($fileName)
	{
		$fileName = explode('/', $fileName);

		return end($fileName);
	}

	public function test_it_is_able_to_create_a_new_instance_for_a_given_data()
	{
		$pictures = $this->pictures();

		$this->assertInstanceOf(Pictures::class, $pictures);
	}

	public function test_it_protects_against_malicious_inputs()
	{
		$pictures = $this->pictures([
			'foo' => 'bar',
			'_pictures_file' => 'the file to be uploaded',
			'_pictures_current' => 'the current file in the server',
			'_pictures_delete' => 'whether the user wants to deleted a current file from the server',
		]);

		$this->assertFalse($pictures->data()->has('foo'));
		$this->assertTrue($pictures->data()->has('_pictures_file'));
		$this->assertTrue($pictures->data()->has('_pictures_delete'));
		$this->assertTrue($pictures->data()->has('_pictures_current'));
	}

	public function test_it_is_able_to_upload_a_file()
	{
		$picture = $this->pictures([
			'_pictures_file' => $this->uploadFile('img/categories'),
		])->store('img/categories');

		Storage::disk('img/categories')->assertExists($this->image($picture));
	}

	public function test_it_is_able_to_update_a_given_file()
	{
		$picture_one = $this->pictures([
			'_pictures_file' => $this->uploadFile('img/categories'),
		])->store('img/categories');

		Storage::disk('img/categories')->assertExists($this->image($picture_one));

		$picture_two = $this->pictures([
			'_pictures_current' => $picture_one,
			'_pictures_delete' => true,
		])->store('img/categories');

		$this->assertNull($picture_two);
		Storage::disk('img/categories')->assertMissing($this->image($picture_one));
	}
}
