<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Users\Parsers;

use Antvel\Tests\TestCase;
use Antvel\Users\Preferences;
use Antvel\Users\Parsers\PreferencesParser;

class PreferencesParserTest extends TestCase
{
	/** @test */
	function it_can_return_the_allowed_keys()
	{
	    tap(PreferencesParser::allowed(), function ($keys) {
		    $this->assertTrue($keys->contains('my_searches'));
		    $this->assertTrue($keys->contains('product_shared'));
		    $this->assertTrue($keys->contains('product_viewed'));
		    $this->assertTrue($keys->contains('product_purchased'));
		    $this->assertTrue($keys->contains('product_categories'));
		    $this->assertInstanceOf('Illuminate\Support\Collection', $keys);
	    });
	}

	/** @test */
	public function it_returns_the_allowed_scheme_if_there_was_no_key_provided()
	{
		$preferences = PreferencesParser::parse();

		$this->assertSame(
			$preferences->toJson(), '{"my_searches":"","product_shared":"","product_viewed":"","product_purchased":"","product_categories":""}'
		);
		$this->assertSame(
			$preferences->toArray(), ['my_searches' => '','product_shared' => '','product_viewed' => '','product_purchased' => '','product_categories' => '']
		);
	}

	/** @test */
	function it_parses_the_signed_user_preferences()
	{
		$user = $this->signIn(['preferences' => $pref = '{"my_searches":"aa","product_shared":"bb","product_viewed":"cc","product_purchased":"dd","product_categories":"ee"}']);

		$preferences = PreferencesParser::parse();

		$this->assertSame($preferences->toJson(), $pref);
		$this->assertSame($preferences->toArray(), $user->preferences);
	}

	/** @test */
	function it_parses_a_given_preferences_input()
	{
		$preferences = PreferencesParser::parse($pref = '{"my_searches":"ff","product_shared":"gg","product_viewed":"hh","product_purchased":"ii","product_categories":"jj"}');

		$this->assertSame(
			$preferences->toJson(), $pref
		);
		$this->assertSame(
			$preferences->toArray(), ['my_searches' => 'ff','product_shared' => 'gg','product_viewed' => 'hh','product_purchased' => 'ii','product_categories' => 'jj']
		);
	}

	public function test_it_can_set_preferences_for_a_given_key()
	{
		$userPreferences = '{"my_searches": "aaa,bbb", "product_shared": "", "product_viewed": "", "product_purchased": "", "product_categories": "8,9"}';

		$products = factory('Antvel\Product\Models\Product', 2)->create([
			'tags' => 'ccc,ddd'
		]);

		$preferences = PreferencesParser::parse($userPreferences)
			->update('my_searches', $products)
			->toArray();

		$productCategoriesIds = $products->pluck('category_id')->implode(',');

		$this->assertEquals($preferences['product_categories'], '8,9,' . $productCategoriesIds);
		$this->assertEquals($preferences['my_searches'], 'ccc,ddd,aaa,bbb');
		$this->assertTrue(trim($preferences['product_purchased']) == '');
		$this->assertTrue(trim($preferences['product_shared']) == '');
		$this->assertTrue(trim($preferences['product_viewed']) == '');
		$this->assertTrue(isset($preferences['my_searches']));
	}

	public function test_it_rejects_keys_that_are_not_allowed()
	{
		$userPreferences = '{"not_allowed":"bad", "my_searches": "aaa,bbb", "product_categories": "8,9"}';

		$products = factory('Antvel\Product\Models\Product')->create([
			'tags' => 'new,tag'
		]);

		$preferences = PreferencesParser::parse($userPreferences)
			->update('not_allowed', $products)
			->toArray();

		$this->assertEquals($preferences['my_searches'], 'aaa,bbb');
		$this->assertEquals($preferences['product_categories'], '8,9');
		$this->assertFalse(array_key_exists('not_allowed', $preferences));
	}

	public function test_it_is_able_to_retrieve_a_given_key()
	{
		$userPreferences = '{"my_searches": "aaa,bbb", "product_categories": "8,9"}';

		$preferences = PreferencesParser::parse($userPreferences)->pluck('my_searches');

		$this->assertInstanceOf('Illuminate\Support\Collection', $preferences);
		$this->assertEquals('aaa,bbb', $preferences->implode(','));
	}

	public function test_it_is_able_to_retrieve_a_given_array_of_keys()
	{
		$userPreferences = '{"my_searches": "aaa,bbb", "product_purchased": "ddd,vvv,aaa", "product_categories": "8,9"}';

		$preferences = PreferencesParser::parse($userPreferences)->all('my_searches');

		$this->assertInstanceOf('Illuminate\Support\Collection', $preferences);
		$this->assertTrue(in_array('aaa', $preferences->get('my_searches')));
		$this->assertTrue(in_array('bbb', $preferences->get('my_searches')));
	}

	public function test_it_is_able_to_retrieve_all_keys()
	{
		$userPreferences = '{"my_searches": "aaa,bbb", "product_purchased": "ddd,vvv,aaa", "product_categories": "8,9"}';

		$preferences = PreferencesParser::parse($userPreferences)->all();

		$this->assertTrue(in_array('ddd', $preferences->get('product_purchased')));
		$this->assertTrue(in_array('8', $preferences->get('product_categories')));
		$this->assertInstanceOf('Illuminate\Support\Collection', $preferences);
		$this->assertTrue(in_array('aaa', $preferences->get('my_searches')));
	}

	/** @test */
	function it_can_update_from_a_given_string()
	{
		$userPreferences = '{"my_searches": "aaa,bbb", "product_purchased": "ccc,ddd", "product_categories": "1,2"}';

		$preferences = PreferencesParser::parse($userPreferences)
			->update('my_searches', 'foo,bar')
			->toArray();

		$this->assertSame($preferences['my_searches'], 'foo,bar,aaa,bbb');
		$this->assertSame($preferences['product_purchased'], 'ccc,ddd');
		$this->assertSame($preferences['product_categories'], '1,2');
	}

	/** @test */
	function it_sets_a_maximum_of_tags_for_keys()
	{
		$current = '{"my_searches":"","product_shared":"","product_viewed":"","product_purchased":"","product_categories":""}';

		$products = factory('Antvel\Product\Models\Product', 100)->make();

		$preferences = PreferencesParser::parse($current)
			->update('my_searches', $products)
			->toArray();

		$searches = explode(',', $preferences['my_searches']);

		$this->assertCount(PreferencesParser::MAX_TAGS, $searches);
	}
}
