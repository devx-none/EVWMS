<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\ReceptionController;


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
Route::apiResource('products', ProductsController::class);
Route::apiResource('deliveries', DeliveryController::class);
Route::apiResource('receipts', ReceptionController::class);
Route::get('/token', function () {
    return csrf_token();
});

