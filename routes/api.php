<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

$protect = [
    'namespace' => 'Api',
    'middleware' => ['auth:api']
];

Route::group($protect, function () {

    Route::get('user', 'UserController@index');

    // category
    Route::resource('/category', 'CategoryController')->except(['create', 'show']);
    Route::get('/categories', 'CategoryController@categories');

    // Product
    Route::resource('/product', 'ProductController')->except(['create']);
    Route::get('/products', 'ProductController@products');

    // Cart
    Route::post('/cart', 'OrderController@addToCart');
    Route::get('/cart', 'OrderController@getCart');
    Route::delete('/cart/{id}', 'OrderController@removeProductFromCart');

    // Customer
    Route::post('/customer/search', 'CustomerController@search');

    // Checkout
    Route::post('/checkout', 'OrderController@store');

    // Order
    Route::post('/order/management', 'OrderController@index');

    // report
    Route::get('/order/pdf/{invoice}', 'OrderController@invoicePdf');
    Route::get('/order/excel/{invoice}', 'OrderController@invoiceExcel');
});
