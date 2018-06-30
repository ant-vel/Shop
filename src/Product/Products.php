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

use Antvel\Support\Repository;
use Antvel\Product\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Antvel\Product\Parsers\FeaturesParser;

class Products extends Repository
{
	use InteractWithPictures;

	/**
	 * Creates a new instance.
	 *
	 * @param Product $product
	 */
	public function __construct(Product $product)
	{
		$this->setModel($product);
	}

	/**
     * Save a new model and return the instance.
     *
     * @param  array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes)
    {
        $attributes = Collection::make($attributes);

        $attr = $attributes->except('features', 'pictures')->merge([
            'features' => \Antvel\Features\Parser::toJson($attributes->get('features')),
            'category_id' => $attributes->get('category'),
            'price' => $attributes->get('price') * 100,
            'cost' => $attributes->get('cost') * 100,
            'status' => $attributes->get('status'),
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
            'tags' => $attributes->get('name'),
        ])->all();

        $product = Product::create($attr);

        $this->createPicturesFor($product, $attributes);

        return $product;
    }

    /**
     * Update a Model in the database.
     *
     * @param array $attributes
     * @param Product|mixed $idOrModel
     * @param array $options
     *
     * @return bool
     */
    public function update(array $attributes, $idOrModel, array $options = [])
    {
    	$product = $this->modelOrFind($idOrModel);
    	$attributes = Collection::make($attributes);

    	$attr = $attributes->except('features', 'pictures', 'default_picture')->merge([
            'features' => \Antvel\Features\Parser::toJson($attributes->get('features')),
            'category_id' => $attributes->get('category'),
            'price' => $attributes->get('price') * 100,
            'cost' => $attributes->get('cost') * 100,
            'status' => $attributes->get('status'),
            'updated_by' => auth()->user()->id,
            'tags' => $attributes->get('name'),
        ])->all();

    	$this->updatePicturesFor($product, $attributes);

    	return $product->update($attr);
    }

	/**
	 * Filters products by a given request.
	 *
	 * @param array $request
	 * @param integer $limit
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function filter($request = [], $limit = null)
	{
		return $this->getModel()
			->with('category')
			->actives() //it needs to go into the query object as well
			->filter($request)
			->orderBy('rate_val', 'desc');
	}
}
