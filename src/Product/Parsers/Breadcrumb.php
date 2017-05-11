<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product\Parsers;

use Illuminate\Support\Collection;

class Breadcrumb
{
	/**
	 * The request information.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Cretaes a new instance.
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	public function __construct(array $data)
	{
		$this->data = Collection::make($data);
	}

	/**
	 * Parses the given collection.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function parse(array $data) : array
	{
		$parser = new static ($data);

		return $parser->all();
	}

	/**
	 * Parses the given data.
	 *
	 * @return array
	 */
	protected function all() : array
	{
		//TODO: refactor this when working on the front end.

		$breadcrumb = $this->data->except('page');

		if ($this->data->has('category')) {
			$breadcrumb = $breadcrumb->merge($this->category());
		}

		return $breadcrumb->all();
	}

	/**
	 * Returns the category associated with the given data.
	 *
	 * @return array
	 */
	protected function category() : array
	{
		$category = explode('|', urldecode($this->data->get('category')));

		return [
			'category' => isset($category[0]) ? $category[0] : 1,
			'category_name' => isset($category[1]) ? $category[1] : '',
		];
	}
}
