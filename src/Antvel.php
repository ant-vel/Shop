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

use Antvel\Support\RoutesRegistrar;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Route;

class Antvel
{
    /**
     * The Antvel Shop version.
     *
     * @var string
     */
    const VERSION = '1.0.9';

    /**
     * The Laravel container component.
     *
     * @var Container
     */
    protected $container = null;

    /**
     * Creates a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->container = Container::getInstance();
    }

    /**
     * Registers Antvel events and listeners.
     *
     * @return void
     */
    public static function events()
    {
        (new \Antvel\Support\EventsRegistrar)->registrar();
    }

    /**
     * Register the antvel policies.
     *
     * @return void
     */
    public static function policies()
    {
        (new \Antvel\Support\PoliciesRegistrar)->registrar();
    }

    /**
     * Get a Antvel route registrar.
     *
     * @param  callable $callback
     * @param  array $options
     *
     * @return void
     */
    public static function routes(callable $callback = null, array $options = [])
    {
        Route::group($options, function ($router) use ($callback) {
            RoutesRegistrar::make($router)->routes($callback);
        });
    }
}
