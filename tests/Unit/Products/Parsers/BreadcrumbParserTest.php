<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Tests\Unit\Products\Parsers;

use Antvel\Tests\TestCase;
use Antvel\Product\Parsers\Breadcrumb;

class BreadcrumbParserTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();
	}

	public function test_it_returns_a_parsed_array()
	{
		$breadcrumb = Breadcrumb::parse([
			'category' => '1|Digital & Music',
			'brands' => 'Microsoft',
			'color' => 'aqua',
		]);

		$this->assertTrue(is_array($breadcrumb));
		$this->assertCount(4, $breadcrumb);
	}

	public function test_it_can_parse_the_category_key()
	{
		$breadcrumb = Breadcrumb::parse([
			'category' => '1|Digital & Music',
		]);

		$this->assertEquals('Digital & Music', $breadcrumb['category_name']);
		$this->assertArrayHasKey('category_name', $breadcrumb);
		$this->assertArrayHasKey('category', $breadcrumb);
		$this->assertEquals('1', $breadcrumb['category']);
	}

	public function test_it_can_parse_the_conditions_key()
	{
		$breadcrumb = Breadcrumb::parse([
			'conditions' => 'new',
		]);

		$this->assertEquals('new', $breadcrumb['conditions']);
		$this->assertArrayHasKey('conditions', $breadcrumb);
	}

	public function test_it_can_parse_the_brands_key()
	{
		$breadcrumb = Breadcrumb::parse([
			'brands' => 'Microsoft'
		]);

		$this->assertEquals('Microsoft', $breadcrumb['brands']);
		$this->assertArrayHasKey('brands', $breadcrumb);
	}

	public function test_it_can_parse_the_colors_key()
	{
		$breadcrumb = Breadcrumb::parse([
			'color' => 'red'
		]);

		$this->assertEquals('red', $breadcrumb['color']);
		$this->assertArrayHasKey('color', $breadcrumb);
	}
}
