<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Users\Models\Concerns;

use Antvel\Orders\Models\Order;

trait Orders
{
	 /**
     * The user's orders. | while refactoring
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * The user's shopping cart. | while refactoring
     *
     * @return mixed
     */
    public function shoppingCart()
    {
        $shoppingCart = $this->orders()->with('details')
            ->where('type', 'cart')
            ->first();

        return is_null($shoppingCart) ? collect() : $shoppingCart->details;
    }

    public function getCartCount()
    {
        return 0;
    }
}
