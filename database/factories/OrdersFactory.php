<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Antvel\Users\Models\User;
use Faker\Generator as Faker;
use Antvel\Orders\Models\Order;
use Antvel\AddressBook\Models\Address;

$factory->define(Order::class, function (Faker $faker)
{
	$seller = User::where('nickname', 'seller')->first();

	return [
        'seller_id' => $seller ? $seller->id : function () { return factory(User::class)->states('seller')->create()->id; },
        'status' => $faker->randomElement(['sent', 'cancelled', 'closed', 'open', 'paid', 'pending', 'received']),
        'address_id' => function () { return factory(Address::class)->create()->id; },
        'user_id' => function () { return factory(User::class)->create()->id;  },
        'type' => $faker->randomElement(['cart', 'wishlist', 'order', 'later']),
        'description' => $faker->text(150),
    ];
});
