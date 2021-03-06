<?php

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


Route::get('/', 'CrazeController@index');
Route::get('/test', 'CrazeController@test');

Route::get('/get', 'CrazeController@get');
Route::get('/unsplash', 'CrazeController@unsplash');
Route::get('/save_images', 'CrazeController@save_images');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
