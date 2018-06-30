<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product;

use Antvel\Http\Controller;
use Illuminate\Http\Request;
use Antvel\Product\Models\Product;

class ProductsGroupingController extends Controller
{
	/**
	 * The products repository.
	 *
	 * @var Products
	 */
	protected $products = null;

    /**
     * Creates a new instance.
     *
     * @param Products $products
     *
     * @return void
     */
	public function __construct(Products $products)
	{
		$this->products = $products;
	}

	/**
	 * Edits the given product grouping.
	 *
	 * @param  Request $request
	 * @param  Product $itemgroup
	 *
	 * @return void
	 */
	public function edit(Request $request, Product $itemgroup)
	{
		$listing = $this->products->filter($request->all());

		return view('dashboard.sections.products.grouping.edit', [
			'getQueryString' => trim($request->getQueryString()) != '' ? $request->getQueryString() . '&' : '',
			'filters' => Parsers\Filters::parse($listing->get()),
			'groupingIds' => $itemgroup->group->pluck('id'),
			'listing' => $listing->paginate(25),
			'product' => $itemgroup,
		]);
	}

	/**
	 * Updates the given product grouping.
	 *
	 * @param  Request $request
	 * @param  Product $itemgroup
	 *
	 * @return void
	 */
	public function update(Request $request, Product $itemgroup)
	{
		$itemgroup->groupWith(
			$request->get('associates')
		);

		return redirect()
			->route('itemgroup.edit', $itemgroup)
			->with('status', trans('globals.success_text'));
	}
}
