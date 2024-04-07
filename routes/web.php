<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');