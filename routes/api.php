<?php

use Illuminate\Support\Facades\Route;


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
    'middleware' => 'auth:api',
], function ($router) {
    Route::apiResource('users', 'UserController');
});


Route::group([
    'namespace' => 'Common',
    'prefix' => 'select',
], function ($router) {
    Route::get('experience', 'Select\SelectController@experience')->name('select.experience');
    Route::get('educations', 'Select\SelectController@educations')->name('select.educations');
    Route::get('schedules', 'Select\SelectController@schedules')->name('select.schedules');
    Route::get('salary', 'Select\SelectController@salary')->name('select.salary');
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

Route::group([
    'namespace' => 'Job',
], function ($router) {
    Route::apiResource('vacancies', 'VacancyController');
    Route::apiResource('category-vacancies', 'CategoryVacancyController');
    Route::apiResource('resume', 'ResumeController');
    Route::apiResource('category-resume', 'CategoryResumeController');
});

Route::group([
    'namespace' => 'Service',
], function ($router) {
    Route::apiResource('services', 'ServiceController');
    Route::apiResource('category-services', 'CategoryServiceController');
});

Route::group([
    'namespace' => 'Ad',
], function ($router) {
    Route::apiResource('ads', 'AdController');
    Route::apiResource('category-ads', 'CategoryAdController');
});
