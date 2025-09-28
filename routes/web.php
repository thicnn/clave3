<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController; // <-- AÑADE ESTA LÍNEA
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\PedidoController; // <-- AÑADE ESTA LÍNEA

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('clientes', ClienteController::class); // <-- Y AÑADE ESTA OTRA LÍNEA AL FINAL

Route::resource('insumos', InsumoController::class);

Route::resource('pedidos', PedidoController::class); // <-- Y AÑADE ESTA OTRA