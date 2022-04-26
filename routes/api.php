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
    Route::post('refresh', 'AuthController@refresh')->withoutMiddleware('auth:api');
    Route::get('user', 'AuthController@user');
});

Route::group([
    'middleware' => ['auth:api'],
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
    Route::get('states', 'Select\SelectController@states')->name('select.states');
    Route::get('reasons', 'Select\SelectController@reasons')->name('select.reasons');
});

Route::group([
    'namespace' => 'Common',
    'prefix' => 'external',
], function ($router) {
    Route::get('find-company', 'External\DadataController@findCompany')->name('dadata.find-company');
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
    Route::put('vacancies/{vacancy}/sort', 'VacancyController@sort')->name('vacancies.sort');
    Route::put('vacancies/{vacancy}/state', 'VacancyController@state')->name('vacancies.state');
    Route::put('vacancies/{vacancy}/restore', 'VacancyController@restore')->name('vacancies.restore');
    Route::apiResource('category-vacancies', 'CategoryVacancyController');
    Route::apiResource('resume', 'ResumeController');
    Route::put('resume/{resume}/restore', 'ResumeController@restore')->name('resume.restore');
    Route::put('resume/{resume}/sort', 'ResumeController@sort')->name('resume.sort');
    Route::put('resume/{resume}/state', 'ResumeController@state')->name('resume.state');
    Route::apiResource('category-resume', 'CategoryResumeController');
});

Route::group([
    'namespace' => 'Service',
], function ($router) {
    Route::apiResource('services', 'ServiceController');
    Route::put('services/{service}/sort', 'ServiceController@sort')->name('services.sort');
    Route::put('services/{service}/state', 'ServiceController@state')->name('services.state');
    Route::put('services/{service}/restore', 'ServiceController@restore')->name('services.restore');
    Route::apiResource('category-services', 'CategoryServiceController');
});

Route::group([
    'namespace' => 'Ad',
], function ($router) {
    Route::apiResource('declarations', 'AdController');
    Route::put('declarations/{declaration}/sort', 'AdController@sort')->name('declarations.sort');
    Route::put('declarations/{declaration}/state', 'AdController@state')->name('declarations.state');
    Route::put('declarations/{declaration}/restore', 'AdController@restore')->name('declarations.restore');
    Route::apiResource('category-declarations', 'CategoryAdController');
});
