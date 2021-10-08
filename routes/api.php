<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user()->load('restaurant');
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'auth'
], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register')->withoutMiddleware('auth:api');
    Route::post('logout', 'AuthController@logout')->withoutMiddleware('auth:api');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('user', 'AuthController@user');
});

Route::apiResource('restaurants', 'RestaurantController');
Route::apiResource('restaurants.foods', 'FoodController')->scoped([
    'restaurantFood' => 'alias',
])->shallow();
Route::get('foods', 'FoodController@foods');

Route::apiResource('orders', 'OrderController');
Route::post('import','FoodController@import');
Route::apiResource('categories', 'CategoryFoodController');
