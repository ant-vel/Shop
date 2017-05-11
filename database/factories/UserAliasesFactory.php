<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Faker\Generator as Faker;
use Antvel\User\Models\{ User, Person, Business };

$factory->defineAs(User::class, 'admin', function (Faker $faker) use ($factory)
{
    return array_merge(
        $factory->raw(User::class), [
            'password' => bcrypt('123456'),
            'email' => 'admin@antvel.com',
            'nickname' => 'gocanto',
            'type' => 'trusted',
            'role' => 'admin',
        ]
    );
});

$factory->defineAs(User::class, 'seller', function (Faker $faker) use ($factory)
{
    return array_merge(
        $factory->raw(User::class), [
            'role' => 'business',
            'type' => 'trusted',
            'nickname' => 'seller',
            'email' => 'seller@antvel.com',
            'password' => bcrypt('123456'),
        ]
    );
});

$factory->defineAs(User::class, 'buyer', function (Faker $faker) use ($factory)
{
    return array_merge(
        $factory->raw(User::class), [
            'role' => 'person',
            'type' => 'trusted',
            'nickname' => 'buyer',
            'email' => 'buyer@antvel.com',
            'password' => bcrypt('123456'),
        ]
    );
});
