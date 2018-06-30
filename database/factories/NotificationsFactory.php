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
use Antvel\Users\Models\User;
use Faker\Generator as Faker;
use Illuminate\Notifications\DatabaseNotification;

$factory->define(DatabaseNotification::class, function (Faker $faker) use ($factory)
{
    return [
        'notifiable_id' => function () { return factory(User::class)->create()->id; },
        'data' => ['status' => 'foo', 'source_id' => '1', 'source_path' => 'antvel', 'label' => ''],
        'notifiable_type' => User::class,
        'type' => stdClass::class,
        'id' =>  uniqid(),
        'read_at' => null,
    ];
});

$factory->state(DatabaseNotification::class, 'unread', function ($faker) {
    return [
        'read_at' => null
    ];
});

$factory->state(DatabaseNotification::class, 'read', function ($faker) {
    return [
        'read_at' => Carbon::now()
    ];
});
