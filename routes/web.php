<?php

use Illuminate\Support\Facades\Auth;
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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// $protect = [
//     'namespace' => 'Api',
//     'middleware' => ['auth']
// ];

// Route::group($protect, function () {
//     Route::get('/order/pdf/{invoice}', 'OrderController@invoicePdf')->name('order.pdf');
//     Route::get('/order/excel/{invoice}', 'OrderController@invoiceExcel')->name('order.excel');
// });

Route::get('{path}', 'HomeController@index')->where('path', '([A-z\d\-\/_.]+)?');
