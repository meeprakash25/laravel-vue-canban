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

Route::get('/info', function(){
   phpinfo();
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::put('column/sync', [\App\Http\Controllers\ColumnController::class, 'sync']);
Route::resource('column', \App\Http\Controllers\ColumnController::class);
Route::resource('card', \App\Http\Controllers\CardController::class);
