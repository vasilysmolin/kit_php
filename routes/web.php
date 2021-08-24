<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Route::get('/', function () {
    return view('welcome');
});
*/
Route::get('/', 'PromoController@promo')->name('promo');
Route::post('/', 'GetEmailController@email')->name('email');
Route::get('/bingo', function () {
    return view('pages.email-success');
})->name('email-success');

