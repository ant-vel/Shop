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
use Antvel\Categories\Models\Category;
use Antvel\Product\Models\{ Product, ProductPictures };

$factory->define(Product::class, function (Faker $faker) use ($factory)
{
    return [
        'category_id' => function () { return factory(Category::class)->create()->id; },
        'created_by' => $user_id = products_factory_user()->id,
        'updated_by' => $user_id,
        'tags' => $faker->word . ',' . $faker->word . ',' . $faker->word,
        'brand' => $faker->randomElement(['Apple', 'Microsoft', 'Samsung', 'Lg']),
        'condition' => $faker->randomElement(['new', 'used', 'refurbished']),
        'low_stock' => $faker->randomElement([5, 10, 15]),
        'sale_counts'  => $faker->randomNumber(9),
        'view_counts'  => $faker->randomNumber(9),
        'stock' => $faker->numberBetween(20, 50),
        'description' => $faker->text(490),
        'name' => $faker->text(90),
        'cost' => rand(100, 500),
        'price' => rand(500, 1000),

        'features' => json_encode([
            trans('globals.product_features.weight') => $faker->numberBetween(10, 150).' '.$faker->randomElement(['Mg', 'Gr', 'Kg', 'Oz', 'Lb']),
            trans('globals.product_features.dimensions') => $faker->numberBetween(1, 30).' X '.
                          $faker->numberBetween(1, 30).' X '.
                          $faker->numberBetween(1, 30).' '.
                          $faker->randomElement(['inch', 'mm', 'cm']),
            trans('globals.product_features.color') => $faker->safeColorName,
        ]),
    ];
});

$factory->define(ProductPictures::class, function (Faker $faker) use ($factory)
{
    return [
        'product_id' => function () {
            return factory(Product::class)->create()->id;
        },
        'path' => '/images/pt-default/' . $faker->numberBetween(1, 330) . '.jpg',
    ];
});

$factory->state(ProductPictures::class, 'default', function ($faker) {
    return [
        'product_id' => function () {
            return factory(Product::class)->create()->id;
        },
        'path' => '/images/pt-default/' . $faker->numberBetween(1, 330) . '.jpg',
        'default' => true
    ];
});

if (! function_exists('products_factory_category')) {
    function products_factory_category()
    {
        $category = Category::inRandomOrder()->first();

        if (is_null($category)) {
            return factory(Category::class)->create()->first();
        }

        return $category;
    }
}

if (! function_exists('products_factory_user')) {
    function products_factory_user()
    {
        $user = User::where('nickname', 'seller')->first();

        if (is_null($user)) {
            return factory(User::class)->states('seller')->create();
        }

        return $user;
    }
}
