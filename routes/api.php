<?php

use App\Http\Controllers\bookingController;
use App\Http\Controllers\breaksController;
use App\Http\Controllers\categoryController;
use App\Http\Controllers\holidayController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('categories', categoryController::class);

Route::post('booking/create', [bookingController::class, 'createBooking']);

Route::get('booking/details', [bookingController::class, 'details']);

Route::resource('breaks', breaksController::class);

Route::resource('holidays', holidayController::class);
