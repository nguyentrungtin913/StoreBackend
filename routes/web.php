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

Route::get('/', function () {
    return view('welcome');
});


//Route::post('/test','App\Http\Controllers\ProductTypeController@test');

//auth
Route::get('/logout','App\Http\Controllers\UserController@logout');
Route::post('/login','App\Http\Controllers\UserController@login');

//productType
Route::get('/product-types','App\Http\Controllers\ProductTypeController@index')->middleware('auth');
Route::get('/product-type','App\Http\Controllers\ProductTypeController@find')->middleware('auth');
Route::post('/product-type','App\Http\Controllers\ProductTypeController@save')->middleware('auth');
Route::put('/product-type','App\Http\Controllers\ProductTypeController@update')->middleware('auth');
Route::delete('/product-type','App\Http\Controllers\ProductTypeController@delete')->middleware('auth');


//product
Route::get('/products','App\Http\Controllers\ProductController@index')->middleware('auth');
Route::get('/product','App\Http\Controllers\ProductController@find')->middleware('auth');
Route::post('/product','App\Http\Controllers\ProductController@save')->middleware('auth');
Route::put('/product','App\Http\Controllers\ProductController@update')->middleware('auth');
Route::delete('/product','App\Http\Controllers\ProductController@delete')->middleware('auth');

//order
Route::post('/order','App\Http\Controllers\OrderController@sell')->middleware('auth');
Route::post('/order-buy','App\Http\Controllers\OrderController@buy');

Route::get('/orders','App\Http\Controllers\OrderController@index')->middleware('auth');
Route::get('/export-orders','App\Http\Controllers\OrderController@exportCsv')->middleware('auth');
Route::get('/order-detail','App\Http\Controllers\OrderController@orderDetail');