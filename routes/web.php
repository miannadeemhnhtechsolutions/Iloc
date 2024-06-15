<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('welcome');
})->middleware('Subscription-package');

Route::get('/view', function () {
    return view('card_details');
});

Route::post('/subscribe',[\App\Http\Controllers\TestController::class,'subscribe']);
Route::get('/login', [\App\Http\Controllers\TestController::class,'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\TestController::class,'login']);
