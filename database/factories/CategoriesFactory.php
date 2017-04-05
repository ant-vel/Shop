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
use Antvel\Categories\Models\Category;

$factory->define(Category::class, function (Faker $faker) use ($factory)
{
    return [
        'name' => $faker->sentence,
        'description' => $faker->paragraph,
        'image' => '/img/pt-default/'.$faker->numberBetween(1, 20).'.jpg',
        'icon' => array_rand(['glyphicon glyphicon-facetime-video', 'glyphicon glyphicon-bullhorn', 'glyphicon glyphicon-briefcase'], 1),
    ];
});
