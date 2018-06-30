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

use Illuminate\Support\Facades\Storage;
use Antvel\Product\Models\{ Product, ProductPictures };

class ProductsPicturesTest extends ProductsTestCase
{
	/** @test */
    function it_can_retrieve_a_given_product_default_picture()
    {
    	$product = factory(Product::class)->create();
    	factory(ProductPictures::class)->create(['product_id' => $product->id]);
    	$default = factory(ProductPictures::class)->states('default')->create(['product_id' => $product->id]);

    	$this->assertEquals($default->path, $product->default_picture);
    }

    /** @test */
    function it_retrieves_the_first_pictures_if_default_is_not_set()
    {
    	$product = factory(Product::class)->create();
    	$pictures = factory(ProductPictures::class, 2)->create(['product_id' => $product->id]);

    	$this->assertSame($pictures->first()->path, $product->default_picture);
    }

    /** @test */
    function it_returns_a_stub_image_if_pictures_were_not_found_when_retrieving_a_product_default_picture()
    {
    	$product = factory(Product::class)->create();

    	$this->assertSame('images/no-image.jpg', $product->default_picture);
    }

    /** @test */
    function it_can_update_the_given_product_default_picture()
    {
    	$product_01 = factory(Product::class)->create();
    	factory(ProductPictures::class)->create(['product_id' => $product_01->id]);
    	factory(ProductPictures::class)->states('default')->create(['product_id' => $product_01->id]);

    	$product_02 = factory(Product::class)->create();
    	factory(ProductPictures::class)->create(['product_id' => $product_02->id]);
    	factory(ProductPictures::class)->states('default')->create(['product_id' => $product_02->id]);

    	$this->assertFalse($product_01->pictures->first()->default);
    	$this->assertTrue($product_01->pictures->last()->default);
    	$this->assertFalse($product_02->pictures->first()->default);
    	$this->assertTrue($product_02->pictures->last()->default);

    	$product_01->updateDefaultPicture(
    		$product_01->pictures->first()->id
    	);

    	$this->assertTrue($product_01->fresh()->pictures->first()->default);
    	$this->assertFalse($product_01->fresh()->pictures->last()->default);
    	$this->assertFalse($product_02->pictures->first()->default);
    	$this->assertTrue($product_02->pictures->last()->default);
    }

    /** @test */
    function it_can_delete_pictures_from_a_given_product()
    {
		$product_01 = factory(Product::class)->create();
    	$picture_01 = factory(ProductPictures::class)->create(['product_id' => $product_01->id]);
    	factory(ProductPictures::class)->states('default')->create(['product_id' => $product_01->id]);

    	$product_02 = factory(Product::class)->create();
    	factory(ProductPictures::class)->create(['product_id' => $product_02->id]);
    	factory(ProductPictures::class)->states('default')->create(['product_id' => $product_02->id]);

    	$this->assertFalse($product_01->pictures->first()->default);
    	$this->assertTrue($product_01->pictures->last()->default);
    	$this->assertFalse($product_02->pictures->first()->default);
    	$this->assertTrue($product_02->pictures->last()->default);

    	$product_01->deletePictures([
    	   $toDelete = $product_01->pictures->first()->id
    	]);

    	$this->assertNull($product_01->fresh()->pictures->where('id', $toDelete)->first());
    	$this->assertCount(1, $product_01->fresh()->pictures);
    	$this->assertCount(2, $product_02->pictures);
    }

    /** @test */
	function a_repository_can_delete_images_from_a_given_product()
	{
        $this->signInAs('seller');

		$product = $this->createProductWithPictures();
		$old_pictures = $product->pictures;

		$data = array_merge($this->data(), [
			'pictures' => [
				'deleting' => [
					$product->pictures->first()->id => true,
					//the second product picture should stay the same.
					$product->pictures->last()->id => true,
				]
			],
		]);

		$this->repository->update($data, $product);
		$new_pictures = $product->fresh()->pictures;

		Storage::persistentFake($disk = 'images/products/' . $product->id);

		$this->assertCount(1, $new_pictures);
		$this->assertFalse(in_array($old_pictures->first()->path, $new_pictures->pluck('path')->toArray()));
		$this->assertFalse(in_array($old_pictures->last()->path, $new_pictures->pluck('path')->toArray()));
		$this->assertSame($old_pictures[1]['path'], $new_pictures->first()->path); //asserting the second picture stayed the same
		Storage::disk($disk)->assertExists($this->image($new_pictures->first()->path));
		Storage::disk($disk)->assertMissing($this->image($old_pictures->last()->path));
		Storage::disk($disk)->assertMissing($this->image($old_pictures->last()->path));

		$this->cleanDirectory($disk);
	}
}
