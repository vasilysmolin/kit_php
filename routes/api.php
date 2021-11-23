<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user()->load('profile.restaurant');
});

Route::group([
    'middleware' => 'auth:api',
    'namespace' => 'Auth',
    'prefix' => 'auth',
], function ($router) {

    Route::post('login', 'AuthController@login')->withoutMiddleware('auth:api');
    Route::post('login-hash', 'AuthController@loginHash')->withoutMiddleware('auth:api');
    Route::post('register', 'AuthController@register')->withoutMiddleware('auth:api');
    Route::post('logout', 'AuthController@logout')->withoutMiddleware('auth:api');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('user', 'AuthController@user');
});

Route::group([
    'namespace' => 'Food',
], function ($router) {
    Route::apiResource('restaurants', 'RestaurantController');
    Route::apiResource('category-restaurants', 'CategoryRestaurantController');
    Route::apiResource('restaurants.dishes', 'DishesController')->scoped([
        'dishes' => 'alias',
    ])->shallow();
    Route::get('dishes', 'DishesController@foods')->name('dishes.foods');
    Route::apiResource('orders', 'OrderController');
    Route::post('import', 'DishesController@import');
    Route::apiResource('categories', 'CategoryFoodController');
});
