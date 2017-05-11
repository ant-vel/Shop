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
use Antvel\Categories\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoriesTest extends TestCase
{
	/**
	 * The categories repository.
	 *
	 * @var \Antvel\Categories\Categories
	 */
	protected $repository = null;

	public function setUp()
	{
		parent::setUp();

		$this->repository = $this->app->make('Antvel\Categories\Categories');
	}

	public function test_it_has_the_correct_model()
	{
		$this->assertNotNull($this->repository->getModel());
		$this->assertInstanceOf(Category::class, $this->repository->getModel());
	}

	public function test_a_repository_can_paginate_result_and_load_it_relationship()
	{
		$parent = factory(Category::class)->create()->first();

		factory(Category::class, 2)->create([
			'category_id' => $parent->id
		]);

		$list = $this->repository->paginateWith('parent.parent');

		$this->assertTrue(count($list) > 0);
		$this->assertCount(3, $list);

		$list->each(function($item) use ($parent) {
			if (! is_null($item->parent)) {
				$this->assertEquals($item->parent->id, $parent->id);
				$this->assertInstanceOf(Category::class, $item->parent);
			}
		});
	}

	public function test_a_repository_can_find_categories_by_a_given_constraints()
	{
		$newCategory = factory(Category::class)->create([
			'name' => 'Games',
			'description' => 'testing lookup by description'
		]);

		//by name and description
		$byNameAndDes = $this->repository->find([
			['description', 'like', '%testing%'],
			['name', 'like', 'Games'],
		])->first();

		$this->assertEquals($byNameAndDes->name, 'Games');
	}

	public function test_a_repository_can_lazy_load_model_relationships()
	{
		$parent = factory(Category::class)->create([
			'name' => 'I am the parent'
		]);

		$children = factory(Category::class, 5)->create([
			'category_id' => $parent->id
		]);

		$category = $this->repository->find($parent->id, '*', 'children')->first();

		$this->assertTrue($category->children->count() > 0);
		$this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $category->children);
	}

	public function test_a_repository_can_create_categories()
	{
		$category = $this->repository->create([
        	'icon' => 'glyphicon glyphicon-facetime-video',
			'_pictures_file' => $this->uploadFile('img/categories'),
			'description' => 'Electronics devices',
			'name' => 'Electronics',
		]);

		//upload assertions
		Storage::disk('img/categories')->assertExists($this->image($category->image));
		$this->assertNotNull($category->image);

		//other assertions
		$this->assertInstanceOf(Category::class, $category);
		$this->assertEquals($category->name, 'Electronics');
	}

	public function test_a_repository_can_create_a_sub_categories()
	{
		$parent = $this->repository->create([
        	'icon' => 'glyphicon glyphicon-facetime-video',
			'description' => 'Electronics devices',
			'_pictures_file' => $this->uploadFile('img/categories'),
			'name' => 'Electronics',
		]);

		$children = factory(Category::class, 2)->create([
			'category_id' => $parent->id
		]);

		//upload assertions
		Storage::disk('img/categories')->assertExists($this->image($parent->image));
		$this->assertNotNull($parent->image);

		//other assertions
		$this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $parent->children);
		$this->assertInstanceOf(Category::class, $parent);
		$this->assertTrue(count($parent->children) > 0);

		$parent->children->each(function($item, $key) use ($parent) {
			$this->assertEquals($item->category_id, $parent->id);
			$this->assertInstanceOf(Category::class, $item);
		});
	}

	public function test_a_repository_can_list_parent_categories()
	{
		factory(Category::class, 2)->create();

		$parents = $this->repository->parents();

		$this->assertCount(2, $parents);
	}

	public function test_a_repository_can_list_parent_categories_except_the_given_one()
	{
		$parents = factory(Category::class, 2)->create();

		$children = factory(Category::class, 2)->create([
			'category_id' => rand(1, 10)
		]);

		$except = factory(Category::class)->create()->first();

		$list = $this->repository->parentsExcept($except->id);

		$this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $list);
		$this->assertCount(2, $list);
	}

	public function test_a_repository_can_update_a_given_category_by_model()
	{
		$category = factory(Category::class)->create(['name' => 'Tools'])->first();

		$wasUpdated = $this->repository->update(['name' => 'Games'], $category);

		$updatedCategory = $this->repository->find($category->id)->first();

		$this->assertEquals($updatedCategory->name, 'Games');
		$this->assertTrue($updatedCategory->name != 'Tools');
		$this->assertTrue($wasUpdated);
	}

	public function test_a_repository_can_update_a_given_category_by_id()
	{
		$category = factory(Category::class)->create(['name' => 'byID'])->first();

		$wasUpdated = $this->repository->update(['name' => 'Books'], $category->id);

		$updatedCategory = $this->repository->find($category->id)->first();

		$this->assertEquals($updatedCategory->name, 'Books');
		$this->assertTrue($updatedCategory->name != 'byID');
		$this->assertTrue($wasUpdated);

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
}
