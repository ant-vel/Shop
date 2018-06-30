<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Carbon\Carbon;
use Faker\Generator as Faker;
use Antvel\Users\Policies\Roles;
use Antvel\Users\Models\{ User, EmailChangePetition };

$factory->define(User::class, function (Faker $faker) use ($factory)
{
    return [
        'first_name' => str_limit($faker->firstName, 60),
        'last_name' => str_limit($faker->lastName, 60),
        'nickname' => str_limit($faker->userName, 60),
        'email' => str_limit($faker->unique()->email, 100),
        'password' => bcrypt('123456'),
        'role' => Roles::default(),
        'phone_number' => str_limit($faker->e164PhoneNumber, 20),
        'gender' => $faker->randomElement(['male', 'female']),
        'birthday' => $faker->dateTimeBetween('-40 years', '-16 years'),
        'image' => '/images/pt-default/'.$faker->numberBetween(1, 20).'.jpg',
        'facebook' => str_limit($faker->userName, 100),
        'twitter' => '@' . str_limit($faker->userName, 100),
        'preferences' => '{"product_viewed":"","product_purchased":"","product_shared":"","product_categories":"","my_searches":""}',
        'verified' => true,
    ];
});

$factory->state(User::class, 'admin', function ($faker) {
    return [
        'first_name' => 'Admin',
        'last_name' => 'Admin',
        'email' => 'admin@antvel.com',
        'nickname' => 'antvel',
        'role' => 'admin',
    ];
});

$factory->state(User::class, 'seller', function ($faker) {
    return [
        'first_name' => 'Seller',
        'last_name' => 'Seller',
        'email' => 'seller@antvel.com',
        'nickname' => 'seller',
        'role' => 'seller',
    ];
});

$factory->state(User::class, 'customer', function ($faker) {
    return [
        'first_name' => 'Customer',
        'last_name' => 'Customer',
        'email' => 'customer@antvel.com',
        'nickname' => 'customer',
        'role' => 'customer',
    ];
});

$factory->define(EmailChangePetition::class, function (Faker $faker) use ($factory)
{
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'expires_at' => Carbon::now()->addWeek(),
        'token' => str_limit($faker->unique()->sha1, 60),
        'old_email' => str_limit($faker->email, 60),
        'new_email' => str_limit($faker->email, 60),
        'confirmed_at' => null,
        'confirmed' => false,
    ];
});
