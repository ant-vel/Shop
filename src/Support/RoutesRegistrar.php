<?php

/*
 * This file is part of the Antvel Shop package.
 *
 * (c) Gustavo Ocanto <gustavoocanto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Antvel\Support;

use Illuminate\Contracts\Routing\Registrar as Router;

class RoutesRegistrar
{
	/**
     * The router implementation.
     *
     * @var Router
     */
    protected $router = null;

    /**
     * The base namespace.
     *
     * @var string
     */
    protected $namespace = 'Antvel';

    /**
     * Create a new route registrar instance.
     *
     * @param  Router  $router
     *
     * @return void
     */
    public static function make(Router $router)
    {
        $registrar = new static;

        $registrar->router = $router;

        return $registrar;
    }

    /**
     * Register routes for Antvel.
     *
     * @param  callable $callback
     *
     * @return void
     */
    public function routes(callable $callback = null)
    {
        $this->forUser();
        $this->forProduct();
        $this->forAddressBook();

        $this->foundation($callback);
    }

    /**
     * Register routes for Antvel foundation panel.
     *
     * @param  callable $callback
     *
     * @return void
     */
    public function foundation (callable $callback = null)
    {
        $callback = $callback ?: function ($registrar) {
            $registrar->forFoundation();
        };

        $callback($this);
    }

    /**
     * Register routes for the User AddressBook.
     *
     * @return void
     */
    protected function forAddressBook()
    {
        $this->router->group([

			'middleware' => ['web', 'auth'],
            'namespace' => $this->namespace . '\AddressBook',

		], function ($router) {

			$router->get('addressBook/', 'AddressBookController@index')->name('addressBook.index');
			$router->put('addressBook/store', 'AddressBookController@store')->name('addressBook.store');
			$router->put('addressBook/{id}', 'AddressBookController@update')->name('addressBook.update');
            $router->get('addressBook/{id}/edit', 'AddressBookController@edit')->name('addressBook.edit');
			$router->get('addressBook/create', 'AddressBookController@create')->name('addressBook.create');
            $router->post('addressBook/delete', 'AddressBookController@destroy')->name('addressBook.delete');
			$router->post('addressBook/default', 'AddressBookController@setDefault')->name('addressBook.default');

        });
    }

    /**
     * Registers routes for the customer component.
     *
     * @return void
     */
    protected function forUser()
    {
        $this->router->group([

            'middleware' => ['web', 'auth'],
            'namespace' => $this->namespace . '\User',

        ], function ($router) {

            $router->resource('user', 'UsersController');

            $this->router->group([

                'middleware' => ['web', 'auth'],
                'prefix' => 'user/security'

            ], function ($router) {

                $router->get('confirmEmail/{token}/{email}', 'SecurityController@confirmEmail')->name('user.email');
                $router->patch('{action}/{user?}', 'SecurityController@update')->name('user.action');

            });

        });
    }

    public function forProduct()
    {
        $this->router->group([

            'middleware' => ['web'],
            'namespace' => $this->namespace . '\Product',

        ], function ($router) {

            $router->get('products', 'Products2Controller@index')->name('products.index');

        });
    }

    /**
     * Registers routes for the admin panel.
     *
     * @return void
     */
    public function forFoundation()
    {
        $this->router->group([

            'middleware' => ['web', 'auth', 'managers'],
            'namespace' => $this->namespace,
            'prefix' => 'foundation',

        ], function ($router) {

            $router->get('dashboard', 'Categories\CategoriesController@index')->name('foundation.home');
            $router->get('/', 'Categories\CategoriesController@index')->name('foundation.home');

            $router->resource('categories', 'Categories\CategoriesController');

        });
    }
}
