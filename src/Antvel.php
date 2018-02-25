<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel;

class Antvel
{
    /**
     * The Antvel Shop version.
     *
     * @var string
     */
    const VERSION = '1.3.0';

    /**
     * All of the service bindings for Antvel.
     *
     * @return array
     */
    public static function bindings()
    {
        return [
            Contracts\CategoryRepositoryContract::class => Categories\Repositories\CategoriesRepository::class,
            Contracts\FeaturesRepositoryContract::class => Features\Repositories\FeaturesRepository::class,
        ];
    }

    /**
     * All of the service aliases for Antvel.
     *
     * @return array
     */
    public static function alias()
    {
        return [
            'category.repository' => Categories\Repositories\CategoriesRepository::class,
            'category.repository.cahe' => Categories\Repositories\CategoriesCacheRepository::class,
            'product.features.repository' => Features\Repositories\FeaturesRepository::class,
            'product.features.repository.cahe' => Features\Repositories\FeaturesCacheRepository::class,
        ];
    }

    /**
     * The Antvel components services providers.
     *
     * @return array
     */
    public static function providers()
    {
        return [
            Categories\CategoriesServiceProvider::class,
            Companies\CompanyServiceProvider::class,
            Users\UsersServiceProvider::class,
        ];
    }

    /**
     * Get the base path of the Antvel installation.
     *
     * @param string $path Optionally, a path to append to the base path
     *
     * @return string
     */
    public static function basePath($path = '')
    {
        return realpath(__DIR__ . '/../') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the resources directory.
     *
     * @param  string  $path
     *
     * @return string
     */
    public static function resourcePath($path = '')
    {
        return self::basePath() . DIRECTORY_SEPARATOR . 'resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the language files.
     *
     * @return string
     */
    public static function langPath()
    {
        return self::resourcePath() . DIRECTORY_SEPARATOR . 'lang';
    }
}
