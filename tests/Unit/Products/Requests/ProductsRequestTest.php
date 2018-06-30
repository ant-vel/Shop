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

use Antvel\Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Antvel\Product\Requests\ProductsRequest;

class ProductsRequestTest extends TestCase
{
	protected function submit($data)
	{
		$request = ProductsRequest::create('/', 'POST', $data);
		$request->setContainer($this->app);

		return $request;
	}

	/** @test */
	function the_product_name_is_required()
	{
		$request = $this->submit(['name' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('name'));
			$this->assertEquals('validation.required', array_first($messages->get('name')));
		});
	}

	/** @test */
	function the_product_description_is_required()
	{
		$request = $this->submit(['description' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('description'));
			$this->assertEquals('validation.required', array_first($messages->get('description')));
		});
	}

	/** @test */
	function the_product_cost_is_required()
	{
		$request = $this->submit(['cost' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('cost'));
			$this->assertEquals('validation.required', array_first($messages->get('cost')));
		});
	}

	/** @test */
	function the_product_status_is_required()
	{
		$request = $this->submit(['status' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('status'));
			$this->assertEquals('validation.required', array_first($messages->get('status')));
		});
	}

	/** @test */
	function the_product_status_has_to_be_boolean()
	{
		$request = $this->submit(['status' => 'foo']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('status'));
			$this->assertEquals('validation.boolean', array_first($messages->get('status')));
		});
	}

	/** @test */
	function the_product_cost_has_to_be_numeric()
	{
	    $request = $this->submit(['cost' => 'bar']);

		$validator = Validator::make($request->all(), $request->rules());

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('cost'));
			$this->assertEquals('validation.numeric', array_first($messages->get('cost')));
		});
	}

	/** @test */
	function the_product_cost_has_to_be_in_the_integers_range()
	{
		$request = $this->submit(['cost' => 1000000000]);

		$validator = Validator::make($request->all(), $request->rules());

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('cost'));
			$this->assertEquals('validation.max.numeric', array_first($messages->get('cost')));
		});
	}

	/** @test */
	function the_product_price_is_required()
	{
		$request = $this->submit(['price' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('price'));
			$this->assertEquals('validation.required', array_first($messages->get('price')));
		});
	}

	/** @test */
	function the_product_price_has_to_be_numeric()
	{
	    $request = $this->submit(['price' => 'bar']);

		$validator = Validator::make($request->all(), $request->rules());

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('price'));
			$this->assertEquals('validation.numeric', array_first($messages->get('price')));
		});
	}

		/** @test */
	function the_product_price_has_to_be_in_the_integers_range()
	{
		$request = $this->submit(['price' => 1000000000]);

		$validator = Validator::make($request->all(), $request->rules());

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('price'));
			$this->assertEquals('validation.max.numeric', array_first($messages->get('price')));
		});
	}

	/** @test */
	function the_product_brand_is_required()
	{
		$request = $this->submit(['brand' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

	    tap($validator->messages(), function ($messages) {
	    	$this->assertTrue($messages->has('brand'));
			$this->assertEquals('validation.required', array_first($messages->get('brand')));
		});
	}

	/** @test */
	function the_product_stock_is_required()
	{
		$request = $this->submit(['stock' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('stock'));
			$this->assertEquals('validation.required', array_first($messages->get('stock')));
		});
	}

	/** @test */
	function the_product_stock_has_to_be_integer()
	{
	    $request = $this->submit(['stock' => 'bar']);

		$validator = Validator::make($request->all(), $request->rules());

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('stock'));
			$this->assertEquals('validation.integer', array_first($messages->get('stock')));
		});
	}

	/** @test */
	function the_product_low_stock_is_required()
	{
		$request = $this->submit(['low_stock' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('low_stock'));
			$this->assertEquals('validation.required', array_first($messages->get('low_stock')));
		});
	}

	/** @test */
	function the_product_low_stock_has_to_be_integer()
	{
	    $request = $this->submit(['low_stock' => 'bar']);

		$validator = Validator::make($request->all(), $request->rules());

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('low_stock'));
			$this->assertEquals('validation.integer', array_first($messages->get('low_stock')));
		});
	}

	/** @test */
	function the_product_category_is_required()
	{
		$request = $this->submit(['category' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('category'));
			$this->assertEquals('validation.required', array_first($messages->get('category')));
		});
	}

	/** @test */
	function the_product_category_has_to_exist_in_the_db()
	{
		$request = $this->submit(['category' => '2']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('category'));
			$this->assertEquals('validation.exists', array_first($messages->get('category')));
		});
	}

	/** @test */
	function the_product_category_has_a_valid_category()
	{
		$category = factory('Antvel\Categories\Models\Category')->create()->id;

		$request = $this->submit(['category' => $category]);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		$this->assertFalse($validator->messages()->has('category'));
	}

	/** @test */
	function the_product_condition_is_required()
	{
		$request = $this->submit(['condition' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('condition'));
			$this->assertEquals('validation.required', array_first($messages->get('condition')));
		});
	}

	/** @test */
	function the_product_condition_has_to_be_valid()
	{
		$request = $this->submit(['condition' => 'foo']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

	    tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('condition'));
			//The product condition has to be either new, used or refurbished
			$this->assertEquals('validation.in', array_first($messages->get('condition')));
		});
	}

	/** @test */
	function it_validates_the_product_pictures_dimensions()
	{
		$request = $this->submit(['pictures' => [
			$this->uploadFile('images/products'),
		]]);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

	    $this->assertFalse($validator->messages()->has('pictures'));
	}

	/** @test */
	function it_builds_dynamic_features_rules_based_upon_request()
	{
		$featureRequired = factory('Antvel\Features\Models\Feature')->states('filterable')->create([
			'name' => 'featureRequired',
			'validation_rules' => 'required'
		]);

		$featureMaxAndMin = factory('Antvel\Features\Models\Feature')->states('filterable')->create([
			'name' => 'featureMaxAndMin',
			'validation_rules' => 'max:20|min:10'
		]);

		$request = $this->submit([
			'features' => [
				'featureRequired' => '',
				'featureMaxAndMin' => 2
			],
		]);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('features.featureRequired'));
			$this->assertTrue($messages->has('features.featureMaxAndMin'));
			$this->assertEquals('validation.required', array_first($messages->get('features.featureRequired')));
			$this->assertEquals('validation.min.string', array_first($messages->get('features.featureMaxAndMin')));
		});
	}
}
