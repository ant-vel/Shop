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
use Antvel\Features\Models\Feature;

$factory->define(Feature::class, function (Faker $faker) use ($factory)
{
    return [
        'product_type'  => 'item',
        'validation_rules' => null,
        'name' => 'Feature Name',
        'input_type' => 'text',
        'help_message' => null,
        'filterable' => false,
        'status' => true,
    ];
});

$factory->state(Feature::class, 'filterable', function ($faker) {
    return [
        'validation_rules' => null,
        'name' => 'Filterable Name',
        'filterable' => true,
    ];
});
