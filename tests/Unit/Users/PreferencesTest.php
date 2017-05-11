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
use Antvel\User\Preferences;

class PreferencesTest extends TestCase
{
	public function test_it_returns_allowed_if__the_key_was_not_provided()
	{
		$preferences = Preferences::parse()->toJson();

		$this->assertEquals($preferences, '{"my_searches":"","product_shared":"","product_viewed":"","product_purchased":"","product_categories":""}');
	}

	public function test_it_can_set_preferences_for_a_given_key()
	{
		$userPreferences = '{"my_searches": "aaa,bbb", "product_shared": "", "product_viewed": "", "product_purchased": "", "product_categories": "8,9"}';

		$products = factory('Antvel\Product\Models\Product', 2)->create([
			'tags' => 'ccc,ddd'
		]);

		$preferences = Preferences::parse($userPreferences)
			->update('my_searches', $products)
			->toArray();

		$this->assertTrue(isset($preferences['my_searches']));
		$this->assertEquals($preferences['my_searches'], 'ccc,ddd,aaa,bbb');
		$this->assertEquals($preferences['product_categories'], '8,9,1');
		$this->assertTrue(trim($preferences['product_purchased']) == '');
		$this->assertTrue(trim($preferences['product_shared']) == '');
		$this->assertTrue(trim($preferences['product_viewed']) == '');
	}

	public function test_it_prunes_keys_that_are_not_allowed()
	{
		$userPreferences = '{"not_allowed":"bad", "my_searches": "aaa,bbb", "product_shared": "", "product_viewed": "", "product_purchased": "", "product_categories": "8,9"}';

		$products = factory('Antvel\Product\Models\Product')->create([
			'tags' => 'new,tag'
		]);

		$preferences = Preferences::parse($userPreferences)
			->update('not_allowed', $products)
			->toArray();

		$this->assertEquals($preferences['my_searches'], 'aaa,bbb');
		$this->assertEquals($preferences['product_categories'], '8,9');
		$this->assertFalse(array_key_exists('not_allowed', $preferences));
	}

	public function test_it_is_able_to_retrieve_a_given_key()
	{
		$userPreferences = '{"my_searches": "aaa,bbb", "product_shared": "", "product_viewed": "", "product_purchased": "", "product_categories": "8,9"}';

		$preferences = Preferences::parse($userPreferences)->pluck('my_searches');

		$this->assertInstanceOf('Illuminate\Support\Collection', $preferences);
		$this->assertEquals('aaa,bbb', $preferences->implode(','));
	}
}
