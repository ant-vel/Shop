<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Users;

use Antvel\Tests\TestCase;
use Antvel\Users\Models\User;
use Antvel\Users\Http\ProfileRequest;
use Illuminate\Support\Facades\Validator;

class ProfileRequestTest extends TestCase
{
	public function setUp()
    {
        parent::setUp();

        $this->actingAs(factory(User::class)->create());
    }

	protected function submit($data)
	{
		$request = ProfileRequest::create('/', 'POST', $data);
		$request->setContainer($this->app);

		return $request;
	}

	/** @test */
	function the_user_first_name_is_required()
	{
		$request = $this->submit(['first_name' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('first_name'));
			$this->assertEquals('validation.required', array_first($messages->get('first_name')));
		});
	}

	/** @test */
	function the_user_last_name_is_required()
	{
		$request = $this->submit(['last_name' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('last_name'));
			$this->assertEquals('validation.required', array_first($messages->get('last_name')));
		});
	}

	/** @test */
	function the_user_gender_is_required()
	{
		$request = $this->submit(['gender' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('gender'));
			$this->assertEquals('validation.required', array_first($messages->get('gender')));
		});
	}

	/** @test */
	function the_user_email_is_required()
	{
		$request = $this->submit(['email' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('email'));
			$this->assertEquals('validation.required', array_first($messages->get('email')));
		});
	}

	/** @test */
	function the_user_email_has_to_be_a_valid_email_address()
	{
		$request = $this->submit(['email' => 'foo']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('email'));
			$this->assertEquals('validation.email', array_first($messages->get('email')));
		});
	}

	/** @test */
	function the_user_email_has_to_be_a_unique_email_address()
	{
		factory(User::class)->create(['email' => 'foo@bar.com']);

		$request = $this->submit(['email' => 'foo@bar.com']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('email'));
			$this->assertEquals('validation.unique', array_first($messages->get('email')));
		});
	}

	/** @test */
	function the_user_nickname_is_required()
	{
		$request = $this->submit(['nickname' => '']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('nickname'));
			$this->assertEquals('validation.required', array_first($messages->get('nickname')));
		});
	}

	/** @test */
	function the_user_nickname_has_to_be_20_characters_max_length()
	{
		$request = $this->submit(['nickname' => 'akjsdhjkshdjkhakjsdhjkahsdkjhajksdhjkahsdjkhajksdhjkahskdjahkd']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('nickname'));
			$this->assertEquals('validation.max.string', array_first($messages->get('nickname')));
		});
	}

	/** @test */
	function the_user_nickname_has_to_be_a_unique()
	{
		factory(User::class)->create(['nickname' => 'gocanto']);

		$request = $this->submit(['nickname' => 'gocanto']);

		$validator = Validator::make(
	    	$request->all(), $request->rules()
	    );

		tap($validator->messages(), function ($messages) {
			$this->assertTrue($messages->has('nickname'));
			$this->assertEquals('validation.unique', array_first($messages->get('nickname')));
		});
	}
}
