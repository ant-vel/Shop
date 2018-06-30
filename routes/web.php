<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Companies')->middleware('web')->group(function ($router) {
	$router->name('about')->get('about/{section?}', 'AboutController@index');

	$router->name('contact.store')->post('contact', 'ContactController@store');
	$router->name('contact')->get('contact', 'ContactController@create');
});

Route::namespace('Support\Images')->group(function ($router) {
	$router->get('images/{file?}', 'RenderController@index')->where('file', '(.*)')->name('images');
});

Route::namespace('Users')->middleware('auth')->group(function ($router) {
	$router->resource('user', 'Http\ProfileController');
    $router->resource('push', 'Http\PushNotificationsController');
    $router->resource('notifications', 'Http\NotificationsController');

    Route::prefix('user/security')->group(function ($router) {
        $router->get('confirmEmail/{token}/{email}', 'Http\SecurityController@confirmEmail')->name('user.email');
        $router->patch('{action}', 'Http\SecurityController@update')->name('user.action');
    });
});

Route::namespace('AddressBook')->middleware('auth')->group(function ($router) {
	$router->resource('addressBook', 'AddressBookController');
	$router->post('addressBook/default', 'AddressBookController@setDefault')->name('addressBook.default');
});

Route::namespace('Product')->group(function ($router) {
	$router->resource('products', 'ProductsController');
    $router->get('productsSearch/', 'SearchController@index')->name('products.search'); //--> see if it can be move to search url
});

Route::prefix('dashboard')->middleware(['auth', 'managers'])->group(function ($router) {
	//Main
    $router->get('/', 'Categories\CategoriesController@index')->name('dashboard.home');

	//Categories
	$router->resource('categories', 'Categories\CategoriesController');

	//Products Features
	$router->resource('features', 'Features\FeaturesController');

	//Items === Products
	$router->resource('items', 'Product\ProductsController');
    $router->resource('itemgroup', 'Product\ProductsGroupingController');
    $router->get('items', 'Product\ProductsController@indexDashboard')->name('items.index');
});
