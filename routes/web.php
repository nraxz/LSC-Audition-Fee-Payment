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

Route::get('/', 'PaymentController@index')->name('index');
Route::post('/paypal', 'PaymentController@payWithpaypal')->name('paypal');

// route for check status of the payment
Route::get('status', 'PaymentController@getPaymentStatus')->name('status');

Route::get('failed', 'PaymentController@failed')->name('failed');

Route::get('cancel', 'PaymentController@cancel')->name('cancel');

Route::get('success', 'PaymentController@success')->name('success');