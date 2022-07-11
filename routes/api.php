<?php

use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;


Route::group([
    'namespace' => 'Auth',
    'prefix' => 'auth',
], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('login-hash', 'AuthController@loginHash');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('user', 'AuthController@user')->middleware('auth:api');

//    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
//        ->middleware('guest')
//        ->name('password.request');
//
//    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
//        ->middleware('guest')
//        ->name('password.email');
//
//    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
//        ->middleware('guest')
//        ->name('password.reset');
//
//    Route::post('/reset-password', [NewPasswordController::class, 'store'])
//        ->middleware('guest')
//        ->name('password.update');
//
//    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
//        ->middleware(['auth:api', 'signed', 'throttle:6,1'])
//        ->name('verification.verify');
//
//    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//        ->middleware(['auth:api', 'throttle:6,1'])
//        ->name('verification.send');
//
//    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
//        ->middleware('auth:api');
});

Route::group([
    'middleware' => ['auth:api'],
], function ($router) {
    Route::apiResource('users', 'UserController');
    Route::put('users/{user}/restore', 'UserController@restore')->name('user.restore')->middleware('role:admin');
    Route::put('users/{user}/sort', 'UserController@sort')->name('user.sort')->middleware('role:admin');
    Route::put('users/{user}/state', 'UserController@state')->name('user.state')->middleware('role:admin');

    Route::apiResource('search-logs', 'Logs\SearchLogsController')
        ->only('index')
        ->middleware('role:admin');
});

Route::group([
], function ($router) {
    Route::apiResource('newletters', 'NewslettersController');
});


Route::group([
    'namespace' => 'Color',
], function ($router) {
    Route::apiResource('colors', 'ColorController')
//        ->middleware('role:admin')
    ;
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
    Route::get('type-services', 'Select\SelectController@typeServices')->name('select.type-services');
});

Route::group([
    'namespace' => 'External',
    'prefix' => 'external',
], function ($router) {
    Route::get('maps', 'YandexMapController@getAddress')->name('external.address');
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
    Route::get('declarations-full', 'AdController@fullSearch')->name('declarations.full-search');
    Route::get('category-declarations-full', 'CategoryAdController@fullSearch')->name('category-declarations.full-search');
    Route::apiResource('category-declarations', 'CategoryAdController');
});

Route::group([
    'namespace' => 'City',
], function ($router) {
    Route::get('cities-full', 'CityController@fullSearch')->name('cities.full-search');
    Route::apiResource('cities', 'CityController');
});

