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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

// Categories
Route::resource('category', 'CategoryController', ['except' => ['create', 'edit']]);
Route::resource('car', 'CarController', ['except' => ['create', 'edit']]);
Route::resource('product', 'ProductController', ['except' => ['create', 'edit']]);