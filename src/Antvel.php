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

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Route;
use Antvel\Foundation\Support\{ EventsRegistrar, PoliciesRegistrar, RoutesRegistrar };

class Antvel
{
    /**
     * The antvel version.
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * Controls whether tests are running.
     *
     * @var boolean
     */
    protected static $testsAreRunning = false;

    /**
     * Checks whether the app user model is valid.
     *
     * @return bool
     */
    public static function doesntHaveUserModel()
    {
        $model = static::user();

        if (is_null($model) || ! class_exists($model)) {
            return true;
        }

        return false;
    }

    /**
     * Returns the applications user model.
     *
     * @return null|App\User
     */
    public static function user()
    {
        if (static::$testsAreRunning) {
            //If phpunit is running, we retrieve the user model stub for testing purposes.
            return \Antvel\Tests\Stubs\User::class;
        }

        $config = Container::getInstance()->make('config');

        return $config->get('auth.providers.users.model');
    }

    /**
     * Tells the application that tests are about to start.
     *
     * @return void
     */
    public static function beginsTests()
    {
        //Allows the application knows whether phpunit is running,
        //so we can swap the user models by the testing one.
        static::$testsAreRunning = true;
    }

    /**
     * Registers Antvel events and listeners.
     *
     * @return void
     */
    public static function events()
    {
        (new EventsRegistrar)->registrar();
    }

    /**
     * Register the antvel policies.
     *
     * @return void
     */
    public static function policies()
    {
        (new PoliciesRegistrar)->registrar();
    }

    /**
     * Get a Antvel route registrar.
     *
     * @param  array  $options
     * @return void
     */
    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        Route::group($options, function ($router) use ($callback) {
            $callback(new RoutesRegistrar($router));
        });
    }
}
