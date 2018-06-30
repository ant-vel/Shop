<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Products;

use Antvel\Product\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductsTest extends ProductsTestCase
{
	/** @test */
	function products_repository_implements_the_correct_model()
	{
	    $this->assertNotNull($this->repository->getModel());
		$this->assertInstanceOf(Product::class, $this->repository->getModel());
	}

	/** @test */
    function can_get_product_cost_in_dollars()
    {
        $product = factory(Product::class)->make([
            'cost' => 6750,
        ]);

        $this->assertEquals('67.50', $product->cost_in_dollars);
    }

	/** @test */
    function can_get_product_price_in_dollars()
    {
        $product = factory(Product::class)->make([
            'price' => 6750,
        ]);

        $this->assertEquals('67.50', $product->price_in_dollars);
    }

	/** @test */
	function a_repository_can_create_new_products()
	{
		$this->signInAs('seller');

		$product = $this->repository->create(array_merge($this->data(), [
			'pictures' => [
				'storing' => [
					$this->uploadFile($disk = 'images/products/1'),
					$this->uploadFile($disk),
				]
			],
		]));

		$this->assertEquals('Foo Bar Biz', $product->description);
		$this->assertEquals('Foo Bar', $product->name);
		$this->assertEquals('foo,bar', $product->tags);
		$this->assertEquals(84900, $product->cost);
		$this->assertEquals(94900, $product->price);

		//assert whether the product features were parsed right.
		$this->assertEquals('8x8x8', $product->features['dimensions']);
		$this->assertEquals('black', $product->features['color']);
		$this->assertEquals(12, $product->features['weight']);

		//assert whether the product pictures exist.
		$this->assertCount(2, $product->pictures);
		foreach ($product->pictures as $picture) {
			Storage::disk($disk)->assertExists(
				$this->image($picture->path)
			);
		}

		$this->cleanDirectory($disk);
	}

	/** @test */
	function a_repository_is_able_to_update_products_data()
	{
		$this->signInAs('seller');

		$product = $this->createProductWithPictures();
		$old_pictures = $product->pictures;

		$data = array_merge($this->data(), [
			'default_picture' => $product->pictures->first()->id,
			'pictures' => [
				'storing' => [
					$product->pictures->first()->id => $this->persistentUpload($disk = 'images/products/' . $product->id),
					//the second product picture should stay the same.
					$product->pictures->last()->id => $this->persistentUpload($disk),
				]
			],
		]);

		$this->repository->update($data, $product);

		$product = $product->fresh();
		$new_pictures = $product->pictures;

		//assertions on product body info.
		$this->assertEquals('Foo Bar Biz', $product->description);
		$this->assertEquals('Foo Bar', $product->name);
		$this->assertEquals('foo,bar', $product->tags);
		$this->assertEquals(94900, $product->price);
		$this->assertEquals(84900, $product->cost);

		//assertions on product features.
		$this->assertEquals('8x8x8', $product->features['dimensions']);
		$this->assertEquals('black', $product->features['color']);
		$this->assertEquals(12, $product->features['weight']);

		//assertions on product pictures.
		$this->assertCount(3, $new_pictures);
		$this->assertTrue($old_pictures[0]['path'] !== $new_pictures[0]['path']); //asserting the first picture was updated
		$this->assertSame($old_pictures[1]['path'], $new_pictures[1]['path']); //asserting the second picture stayed the same
		$this->assertTrue($old_pictures[2]['path'] !== $new_pictures[2]['path']); //asserting the last picture was updated

		foreach ($new_pictures as $picture) {
			Storage::disk($disk)->assertExists($this->image($picture['path']));
		}

		$this->cleanDirectory($disk);
	}

	/** @test */
	function it_is_able_to_inactivate_a_given_product()
	{
		$this->signInAs('seller');

		$product = factory(Product::class)->create();

		$this->repository->update(array_merge($this->data(), [
			'status' => false
		]), $product);

		$this->assertFalse($product->fresh()->status);
	}

	/** @test */
	function it_is_able_to_activate_a_given_product()
	{
		$this->signInAs('seller');

		$product = factory(Product::class)->create(['status' => false]);

		$this->repository->update(array_merge($this->data(), [
			'status' => true
		]), $product);

		$this->assertTrue($product->fresh()->status);
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	function it_is_able_to_retrieve_a_list_of_products_by_a_given_features_key()
	{
		$this->usingMySql();

		factory(Product::class)->create(['features' => '{"color": "white", "weight": "11"}']);
		factory(Product::class)->create(['features' => '{"weight": "12"}']);

		$products = Product::byFeaturesKey('color')->get();

		$this->assertCount(1, $products);
		$this->assertEquals('11', $products->first()->features['weight']);
		$this->assertEquals('white', $products->first()->features['color']);

		$this->artisan('migrate:reset', ['--database' => self::TESTING_DB]);
	}
}
