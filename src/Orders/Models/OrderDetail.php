<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Orders\Models;

use Antvel\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'quantity',
        'status',
        'delivery_date',
        'rate',
        'rate_comment',
    ];

    protected $appends = ['product'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getProductAttribute()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->first();
    }

    public function scopeReviewsFor($query, $product_id)
    {
        return $query->where('product_id', $product_id)
            ->whereNotNull('rate_comment')
            ->select('rate', 'rate_comment', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
    }
}
