<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Antvel\User\Models\User;
use Faker\Generator as Faker;
use Antvel\AddressBook\Models\Address;

$factory->define(Address::class, function (Faker $faker) use ($factory)
{
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'default' => 0,
        'city' => $faker->city,
        'state' => $faker->state,
        'country' => $faker->country,
        'zipcode' => $faker->postcode,
        'line1' => $faker->streetAddress,
        'line2' => $faker->streetAddress,
        'phone' => $faker->e164PhoneNumber,
        'name_contact' => $faker->streetName,
    ];
});
