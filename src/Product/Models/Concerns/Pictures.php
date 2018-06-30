<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product\Models\Concerns;

trait Pictures
{
	/**
     * Returns pictures of a given product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pictures()
    {
        return $this->hasMany('Antvel\Product\Models\ProductPictures');
    }

	/**
     * Update the model pictures.
     *
     * @param  array $pictures
     *
     * @return array
     */
    public function updatePictures($pictures)
    {
        foreach ($pictures as $picture) {
            $image = $this->pictures()->where('id', $picture['id']);

            if ($image->exists()){
               $image->update(['path' => $picture['path']]);
            } else {
               $this->pictures()->create(['path' => $picture['path']]);
            }
        }
    }

    /**
     * Deletes the given pictures from the model.
     *
     * @param  array $ids
     *
     * @return void
     */
    public function deletePictures($ids)
    {
        $pictures = $this->pictures()->whereIn('id', $ids)->get();

        $pictures->each(function ($item) {
            $item->delete();
        });
    }

    /**
     * Updates the given product default picture.
     *
     * @param  integer $pictureId
     *
     * @return void
     */
    public function updateDefaultPicture($pictureId)
    {
    	if ($pictureId) {
        	$this->pictures()->update(['default' => false]);
        	$this->pictures()->where('id', $pictureId)->update(['default' => true]);
    	}
    }

    /**
     * Returns the product default picture.
     *
     * @return string
     */
    public function getDefaultPictureAttribute()
    {
        $default = $this->pictures->where('default', true)->first();

        if ($default) {
            return $default->path;
        }

        $picture = $this->pictures->first();

        if (is_null($picture)) {
            return 'images/no-image.jpg';
        }

        return $picture->path;
    }
}
