<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\PedidoController;

// Ruta de bienvenida pública
Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación generadas por Laravel (login, register, etc.)
Auth::routes();

// Grupo de rutas protegidas que requieren autenticación
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Rutas para el CRUD de Clientes
    Route::resource('clientes', ClienteController::class);

    // Rutas para el CRUD de Insumos
    Route::resource('insumos', InsumoController::class);

    // Rutas para el CRUD de Pedidos
    Route::resource('pedidos', PedidoController::class);

    // Ruta específica para eliminar archivos adjuntos de un pedido
    Route::delete('/pedidos/{pedido}/archivos/{archivo}', [PedidoController::class, 'destroyArchivo'])
        ->name('pedidos.archivos.destroy');
});