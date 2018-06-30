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
use Antvel\Features\Models\Feature;
use Illuminate\Support\Facades\Auth;
use Antvel\Categories\Models\Category;
use Antvel\Product\Requests\ProductsRequest;
use Antvel\Support\Images\Manager as Images;
use Antvel\Orders\Models\{ Order, OrderDetail };
use Antvel\Product\Repositories\ProductsRepository;

class ProductsController extends Controller
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
		//I need to come back in here and check how I can sync the paginated products
		//with the filters. The issue here is paginated does not contain the whole
		//result, therefore, the filters count are worng.

		$products = $this->products->filter(
			$request->all()
		);

		// this line is required in order for the store to show
		// the counter on the side bar filters.
		$allProducts = $products->get();

		if (Auth::check()) {
			Auth::user()->updatePreferences('my_searches', $allProducts);
		}

		return view('products.index', [
			'suggestions' => Suggestions\Suggest::shakeFor($allProducts),
			'refine' => Parsers\Breadcrumb::parse($request->all()),
			'filters' => Parsers\Filters::parse($allProducts),
			'products' => $products->paginate(28),
			'panel' => $this->panel,
		]);
	}

	/**
	 * List the seller products.
	 *
	 * @return void
	 */
	public function indexDashboard(Request $request)
	{
		$products = $this->products->filter($request->all())
			->with('creator', 'updater')
			->paginate(20);

		return view('dashboard.sections.products.index', [
			'products' => $products,
		]);
	}

	/**
     * Display the given product.
     *
     * @param Product $product
     *
     * @return void
     */
    public function show(Product $product)
    {
        $product->load('group', 'category');

        //increasing product counters, in order to have a suggestion orden
        (new ProductsRepository)->increment('view_counts', $product);

        //saving the product tags into users preferences
        if (trim($product->tags) != '' && auth()->check()) {
            auth()->user()->updatePreferences('product_viewed', $product->tags);
        }

        return view('products.detailProd', [
            'suggestions' => Suggestions\Suggest::for('product_viewed')->shake()->get('product_viewed'),
            'reviews' => OrderDetail::ReviewsFor($product->id),
            'allWishes' => Order::forSignedUser('wishlist'),
            'features' => Feature::filterable()->get(),
            'product' => $product,
        ]);
    }

	/**
	 * Show the creating form.
	 *
	 * @param  Feature $features
	 *
	 * @return void
	 */
	public function create(Feature $features)
	{
		return view('dashboard.sections.products.create', [
			'conditions' => Attributes::make('condition')->get(),
			'features' => $features->filterable()->get(),
			'categories' => Category::actives()->get(),
			'MAX_PICS' => Images::MAX_PICS,
		]);
	}

	/**
	 * Stores a new product.
	 *
	 * @param  ProductsRequest $request
	 *
	 * @return void
	 */
	public function store(ProductsRequest $request)
	{
		$product = $this->products->create(
			$request->all()
		);

		return redirect()->route('items.edit', [
			'item' => $product->id
		])->with('status', trans('globals.success_text'));
	}

	/**
	 * Show the editing form.
	 *
	 * @param  Models\Product $item
	 * @param  Feature $features
	 *
	 * @return void
	 */
	public function edit(Models\Product $item, Feature $features)
	{
		return view('dashboard.sections.products.edit', [
			'MAX_PICS' => Images::MAX_PICS - $item->pictures->count(),
			'conditions' => Attributes::make('condition')->get(),
			'features' => $features->filterable()->get(),
			'categories' => Category::actives()->get(),
			'item' => $item,
		]);
	}

	/**
     * Updates the given product.
     *
     * @param  ProductsRequest $request
     * @param  integer $item
     *
     * @return void
     */
	public function update(ProductsRequest $request, $item)
	{
		$this->products->update(
			$request->all(), $item
		);

		return back()->with('status', trans('globals.success_text'));
	}
}
