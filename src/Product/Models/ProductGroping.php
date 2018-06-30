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

use Illuminate\Database\Eloquent\Model;

class ProductGroping extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products_grouping';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'associated_id', 'created_at', 'updated_at'
    ];
}
