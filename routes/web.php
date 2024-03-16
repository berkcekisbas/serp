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
Route::get('/online', [\App\Http\Controllers\SerpController::class, 'online']);
Route::get('/', [\App\Http\Controllers\SerpController::class, 'index']);
Route::get('/post', [\App\Http\Controllers\SerpController::class, 'index2']);
Route::get('/test', [\App\Http\Controllers\TestController::class, 'test']);
Route::post('/test2', [\App\Http\Controllers\TestController::class, 'test2']);
