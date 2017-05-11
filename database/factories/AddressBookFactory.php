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
        'city' => str_limit($faker->city, 100),
        'state' => str_limit($faker->state, 100),
        'country' => str_limit($faker->country, 100),
        'zipcode' => str_limit($faker->postcode, 20),
        'line1' => str_limit($faker->streetAddress, 250),
        'line2' => str_limit($faker->streetAddress, 250),
        'phone' => str_limit($faker->e164PhoneNumber, 20),
        'name_contact' => str_limit($faker->streetName, 100),
    ];
});

$factory->defineAs(Address::class, 'buyer', function (Faker $faker)  use ($factory)
{
    return array_merge(
        $factory->raw(Address::class), [
            'user_id' => 4,
        ]
    );
});
