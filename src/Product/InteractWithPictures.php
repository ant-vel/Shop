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

use Illuminate\Support\Collection;
use Antvel\Support\Images\Manager as Images;

trait InteractWithPictures
{
	/**
	 * The directory where to save the given images.
	 *
	 * @var string
	 */
	protected $basePath = 'images/products';

	/**
	 * Creates pictures for the given product.
	 *
	 * @param  \Antvel\Product\Models\Product $product
	 * @param  array $attr
	 *
	 * @return void
	 */
	protected function createPicturesFor($product, $attr)
    {
        $pictures = Images::parse($attr->get('pictures'))
            ->on($this->basePath. '/' . $product->id)
            ->store();

        $product->pictures()->createMany($pictures);
    }

    /**
     * Updates pictures for the given product.
     *
     * @param  \Antvel\Product\Models\Product $product
     * @param  array $attr
     *
     * @return void
     */
    protected function updatePicturesFor($product, $attr)
    {
        $current = $product->pictures;
        $images = Images::parse($pictures = $attr->get('pictures'))->on($this->basePath . '/' . $product->id);

        //We check whether the request wants deletion, if so
        //We delete the files and records related to it.
    	if ($images->wantsDeletion()) {
    		$this->deletePictures($product, $images->delete($current));
    	}

        //We check whether there was any petition to update pictures, if so,
        //We retrieve the files related to the given request and proceed
        //to update the product pictures records.
        if (isset($pictures['storing']) && count($pictures['storing']) > 0) {

            //We select the current product pictures that are included in the
            //request, so we will not delete unrequested pictures.
            $current = $current->whereIn('id', array_keys($pictures['storing']));

            //We delete the files related to the request and return the new info
            //to be saved for the given product.
            $pictures = $images->update($current);

            //if the retrieved pictures were not an array of pictures,
            //we wrap them in an array to fulfill this requirement.
        	$product->updatePictures(
                isset($pictures['path']) ? [$pictures] : $pictures
            );
        }

        if (isset($attr['default_picture'])) {
            $product->updateDefaultPicture($attr['default_picture']);
        }
    }

    /**
     * Delete the given pictures from the give product.
     *
     * @param  \Antvel\Product\Models\Product $product
     * @param  array  $toDelete
     *
     * @return void
     */
    protected function deletePictures($product, $toDelete = [])
    {
    	if (count($toDelete) > 0) {
            $product->deletePictures($toDelete);
    	}
    }
}
