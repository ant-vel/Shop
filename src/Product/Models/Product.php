<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Product\Models;

use Antvel\Product\QueryFilter;
use Antvel\Categories\Models\Category;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['num_of_reviews'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'user_id', 'name', 'description', 'price',
        'stock', 'features', 'barcode', 'condition', 'rate_val',
        'rate_count', 'low_stock', 'status', 'parent_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['details', 'created_at'];

    /**
     * Returns the category of the product.
     *
     * @return Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Filter users upon type requested.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $request
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $request)
    {
        return (new QueryFilter($request))->apply($query);
    }

    /**
     * Returns suggestions for a given constraints.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $constraints
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuggestionsFor($query, $tags)
    {
        if (count($tags) > 0) {
            $query->where(function ($builder) use ($tags) {
                    foreach ($tags as $tag) {
                        $builder->orWhere('tags', 'like', '%' . $tag . '%');
                    }
                    return $builder;
                });
        }

        return $query;
    }

    /**
     * Returns the actives products.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeActives($query)
    {
        return $query->where('status', 1)->where('stock', '>', 0);
    }

    /**
     * Returns the features product transformed to an array.
     *
     * @return array
     */
    public function getFeaturesAttribute()
    {
        return json_decode($this->attributes['features'], true);
    }

    /////////// while refactoring
    public function getNumOfReviewsAttribute()
    {
        return $this->rate_count.' '.\Lang::choice('store.review', $this->rate_count);
    }


}
