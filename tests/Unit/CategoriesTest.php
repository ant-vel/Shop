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

class CategoriesTest extends TestCase
{
	protected $repository = null;

	public function setUp()
	{
		parent::setUp();

		$this->repository = $this->app->make('Antvel\Categories\Categories');
	}

	public function test_a_repository_can_find_categories_by_a_given_constraints()
	{
		$newCategory = factory(Category::class)->create([
			'name' => 'Games',
			'description' => 'testing lookup by description'
		]);

		//by name
		$byName = $this->repository->find([
			['name', 'like', 'Games']
		], ['name'])->first();

		//by description
		$byDes = $this->repository->find([
			['description', 'like', '%testing%']
		], ['description'])->first();

		//by name and description
		$byNameAndDes = $this->repository->find([
			['description', 'like', '%testing%'],
			['name', 'like', 'Games'],
		])->first();

		$this->assertEquals($byDes->description, 'testing lookup by description');
		$this->assertEquals($byNameAndDes->name, 'Games');
		$this->assertEquals($byName->name, 'Games');

		$this->assertNull($byName->description);
		$this->assertNull($byDes->name);
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
		$this->repository->create([
			'name' => 'Electronics',
			'description' => 'Electronics devices',
			'image' => '/img/pt-default/'.mt_rand(1, 20).'.jpg',
        	'icon' => array_rand(['glyphicon glyphicon-facetime-video', 'glyphicon glyphicon-bullhorn', 'glyphicon glyphicon-briefcase'], 1),
		]);

		$created = $this->repository->find([
			['name', 'like', 'Electronics']
		], ['name']);

		$created = $created->first();

		$this->assertEquals($created->name, 'Electronics');
		$this->assertInstanceOf(Category::class, $created);
	}

	public function test_a_repository_can_create_a_sub_categories()
	{
		$parent = $this->repository->create([
			'name' => 'Electronics',
			'description' => 'Electronics devices',
			'image' => '/img/pt-default/'.mt_rand(1, 20).'.jpg',
        	'icon' => array_rand(['glyphicon glyphicon-facetime-video', 'glyphicon glyphicon-bullhorn', 'glyphicon glyphicon-briefcase'], 1),
		]);

		$children = factory(Category::class, 5)->create([
			'category_id' => $parent->id
		]);

		$this->assertTrue(count($parent->children) > 0);
		$this->assertEquals($parent->name, 'Electronics');
		$this->assertInstanceOf(Category::class, $parent);
		$this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $parent->children);

		$parent->children->each(function($item, $key) use ($parent) {
			$this->assertEquals($item->category_id, $parent->id);
			$this->assertInstanceOf(Category::class, $item);
		});
	}
}
