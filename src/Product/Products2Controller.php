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
use Antvel\Support\Paginator;
use Antvel\Product\Parsers\Filters as FiltersParser;
use Antvel\Product\Parsers\Breadcrumb as BreadcrumbParser;

class Products2Controller extends Controller
{
	/**
	 * The products repository.
	 *
	 * @var Products
	 */
	protected $products = null;

	protected $panel = [
        'left'   => ['width' => '2', 'class'=>'categories-panel'],
        'center' => ['width' => '10'],
    ];

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
	 * Loads the foundation dashboard.
	 *
	 * @return void
	 */
	public function index(Request $request)
	{
		$products = $this->products->filter($request->all());

		return view('products.index', [
			'products' => Paginator::trace($request)->paginate($products, 28),
			'suggestions' => $this->products->suggestFor($products),
			'refine' => BreadcrumbParser::parse($request->all()),
			'filters' => FiltersParser::parse($products),
			'panel' => $this->panel,
		]);
	}
}
