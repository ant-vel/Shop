<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Categories;

use Antvel\Tests\TestCase;
use Antvel\Categories\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryTest extends TestCase
{
	/** @test */
	function a_category_has_children()
	{
	    $parentA = factory(Category::class)->create();
	    $childA = factory(Category::class)->create(['category_id' => $parentA->id]);

	    $parentB = factory(Category::class)->create();

	    $this->assertCount(1, $parentA->children);
	    $this->assertSame($childA->id, $parentA->children->first()->id);
	    $this->assertInstanceOf(Category::class, $parentA->children->first());
	    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $parentA->children);

	    $this->assertCount(0, $parentB->children);
	    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $parentB->children);
	}

	/** @test */
	function a_category_has_one_parent()
	{
		$parentA = factory(Category::class)->create(['name' => 'parentA']);
	    $childA = factory(Category::class)->create(['category_id' => $parentA->id]);
	    $childB = factory(Category::class)->create(['category_id' => null]);

	    $this->assertInstanceOf(Category::class, $childA->parent);
	    $this->assertEquals('parentA', $childA->parent->name);
	    $this->assertNull($childB->parent);
	}

	/** @test */
	function parents_categories_can_be_listed()
	{
		$parentA = factory(Category::class)->create();
		factory(Category::class)->create();
		factory(Category::class)->create(['category_id' => $parentA->id]);

		tap(Category::parents()->get(), function ($parents) {
			$this->assertCount(2, $parents);
			foreach ($parents as $parent) {
				$this->assertNull($parent->category_id);
			}
		});
	}

	/** @test */
	function it_set_a_category_image_using_the_pictures_field()
	{
		$category = Category::create([
            'name' => 'new name',
            'description' => 'new description',
            'icon' => 'new icon'
		]);

		$category->pictures = [
            'storing' => $this->uploadFile($disk = 'images/categories/' . $category->id),
        ];

        $category->save();

		tap($category->fresh(),  function($category) use ($disk) {
			Storage::disk($disk)->assertExists($this->image($category->image));
			$this->assertNotNull($category->image);
		});
	}

	/** @test */
	function it_can_update_a_given_category_image_using_the_pictures_field()
	{
		$category = factory(Category::class)->create();

		$category->pictures = [
			'storing' => [
				$this->uploadFile($disk = 'images/categories/' . $category->id),
			]
		];

		$category->save();
		tap($category->fresh(),  function($category) use ($disk) {
			Storage::disk($disk)->assertExists($this->image($category->image));
			$this->assertNotNull($category->image);
		});

		//updating the given category
		$oldImage = $category->image;
		$category->pictures = [
			'storing' => [
				$this->persistentUpload($disk),
			]
		];

		$category->save();
		tap($category->fresh(),  function($category) use ($disk, $oldImage) {
			Storage::disk($disk)->assertExists($this->image($category->image));
			Storage::disk($disk)->assertMissing($this->image($oldImage));
			$this->assertNotNull($category->image);
		});

		$this->cleanDirectory($disk);
	}

	/** @test */
	function it_can_delete_a_given_category_image_using_the_pictures_field()
	{
		$category = factory(Category::class)->create();

		$category->pictures = [
			'storing' => [
				$this->uploadFile($disk = 'images/categories/' . $category->id),
			]
		];

		$category->save();
		tap($category->fresh(),  function($category) use ($disk) {
			Storage::disk($disk)->assertExists($this->image($category->image));
			$this->assertNotNull($category->image);
		});

		$oldImage = $category->image;

		$category->pictures = [
			'deleting' => [
				$category->id => true,
			]
		];

		$category->save();
		tap($category->fresh(),  function($category) use ($disk, $oldImage) {
			Storage::disk($disk)->assertMissing($this->image($oldImage));
			$this->assertNull($category->image);
		});
	}
}
