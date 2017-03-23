<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Antvel\Antvel;
use Faker\Generator as Faker;
use Antvel\User\Models\{ Person, Business, EmailChangePetition };

$factory->define(Antvel::user(), function (Faker $faker) use ($factory)
{
    return [
        'password' => bcrypt('123456'),
        'nickname' => $faker->userName,
        'facebook' => $faker->userName,
        'twitter' => '@'.$faker->userName,
        'email' => $faker->unique()->email,
        'role' => array_rand(resolveTrans('globals.roles'), 1),
        'pic_url' => '/img/pt-default/'.$faker->numberBetween(1, 20).'.jpg',
        'preferences' => '{"product_viewed":[],"product_purchased":[],"product_shared":[],"product_categories":[],"my_searches":[]}',
    ];
});

$factory->define(Person::class, function (Faker $faker) use ($factory)
{
    return [
        'user_id' => function () {
            return factory(Antvel::user())->create()->id;
        },
        'last_name' => $faker->lastName,
        'first_name' => $faker->firstName,
        'home_phone' => $faker->e164PhoneNumber,
        'gender' => $faker->randomElement(['male', 'female']),
        'birthday' => $faker->dateTimeBetween('-40 years', '-16 years')
    ];
});

$factory->define(Business::class, function (Faker $faker) use ($factory)
{
    return [
        'user_id' => function () {
            return factory(Antvel::user())->create()->id;
        },
        'creation_date' => $faker->date(),
        'business_name' => $faker->company,
        'local_phone' => $faker->e164PhoneNumber,
    ];
});

$factory->define(EmailChangePetition::class, function (Faker $faker) use ($factory)
{
    //str_random
    return [
        'user_id' => function () {
            return factory(Antvel::user())->create()->id;
        },
        'old_email' => $faker->email,
        'new_email' => $faker->email,
        'token' => $faker->unique()->randomDigit,
        'confirmed' => '0',
        'expires_at' => Carbon::now(),
        'confirmed_at' => ''
    ];
});
