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

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('forgot-password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('reset-password.email');

    Route::get('/verify-email', [VerifyEmailController::class, 'show'])
        ->middleware(['auth:api', 'throttle:6,1'])
        ->name('verify-email.send');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth:api', 'throttle:6,1'])
        ->name('verification-notification.send');
//
//    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
//        ->middleware('auth:api');
});

Route::group([
    'middleware' => ['auth:api'],
], function ($router) {
    Route::get('users/accounts', 'UserController@accounts')->name('users.accounts');
    Route::get('users/accounts/current', 'UserController@currentAccount')->name('users.current.account');
    Route::put('users/change-profile', 'UserController@changeProfile')->name('users.change-profile');
    Route::get('users/download', 'UserController@download')->name('users.download')->middleware('role:admin');
    Route::put('users/{user}/restore', 'UserController@restore')->name('users.restore')->middleware('role:admin');
    Route::put('users/{user}/sort', 'UserController@sort')->name('users.sort')->middleware('role:admin');
    Route::put('users/{user}/state', 'UserController@state')->name('users.state')->middleware('role:admin');
    Route::get('users/check-user/{email}', 'UserController@checkUser')->name('users.check-user');
    Route::put('users/add-user/{email}', 'UserController@addUser')->name('users.add-user');
    Route::delete('users/delete-user/{email}', 'UserController@deleteUser')->name('users.delete-user');
    Route::apiResource('invited-users', 'InvitedUserController');
    Route::apiResource('users', 'UserController');

    Route::apiResource('search-logs', 'Logs\SearchLogsController')
        ->only('index')
        ->middleware('role:admin');
});

Route::group([
], function ($router) {
    Route::get('newsletters/download', 'NewslettersController@download')->name('newsletters.download');
    Route::apiResource('newsletters', 'NewslettersController');
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
    Route::get('find-address', 'External\DadataController@findAddress')->name('dadata.find-address');
});

Route::group([
    'namespace' => 'Feed',
], function ($router) {
    Route::apiResource('feeds', 'FeedController');
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
    Route::apiResource('category-declarations', 'CategoryAdController');
    Route::put('declarations/{declaration}/sort', 'AdController@sort')->name('declarations.sort');
    Route::put('declarations/{declaration}/state', 'AdController@state')->name('declarations.state');
    Route::put('declarations/{declaration}/restore', 'AdController@restore')->name('declarations.restore');
    Route::get('declarations-full', 'AdController@fullSearch')->name('declarations.full-search');
    Route::get('category-declarations-full', 'CategoryAdController@fullSearch')->name('category-declarations.full-search');
});

Route::group([
    'namespace' => 'Realty',
], function ($router) {
    Route::apiResource('realties', 'RealtyController');
    Route::apiResource('category-realties', 'CategoryRealtyController');
    Route::put('realties/{realty}/sort', 'RealtyController@sort')->name('realties.sort');
    Route::put('realties/{realty}/state', 'RealtyController@state')->name('realties.state');
    Route::put('realties/{realty}/restore', 'RealtyController@restore')->name('realties.restore');
    Route::post('realties/import', 'RealtyController@import')->name('realties.import');
    Route::get('realties-full', 'RealtyController@fullSearch')->name('realties.full-search');
    Route::get('category-realties-full', 'CategoryRealtyController@fullSearch')->name('category-realties.full-search');
});

Route::group([
    'namespace' => 'Journal',
], function ($router) {
    Route::apiResource('journals', 'JournalController');
});

Route::group([
    'namespace' => 'City',
], function ($router) {
    Route::get('cities-full', 'CityController@fullSearch')->name('cities.full-search');
    Route::apiResource('cities', 'CityController');
});

