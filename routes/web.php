<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin;

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
});


require __DIR__.'/auth.php';


Route::middleware(['auth:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home'); // admin.home ルートを定義
    Route::resource('users', Admin\UserController::class);
    Route::resource('restaurants', Admin\RestaurantController::class);

    Route::resource('categories', Admin\CategoryController::class);
});

