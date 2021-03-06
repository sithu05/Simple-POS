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

Route::resource('supplier', 'SupplierController', ['except' => ['create', 'edit']]);

Route::resource('customer', 'CustomerController', ['except' => ['create', 'edit']]);

Route::resource('purchase', 'PurchaseController', ['except' => ['create', 'edit']]);

Route::resource('sale', 'SaleController', ['except' => ['create', 'edit']]);

Route::get('stock', 'StockController@index');
Route::get('stock/data', 'StockController@getData');