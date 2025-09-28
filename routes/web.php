<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PedidoItemController;
use App\Http\Controllers\MaquinaController;
use App\Http\Controllers\ProductoController;

// Ruta de bienvenida pública
Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación generadas por Laravel (login, register, etc.)
Auth::routes();

// Grupo de rutas protegidas que requieren autenticación
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', function() {
        return redirect()->route('dashboard');
    })->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reportes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/ventas', [ReportController::class, 'ventas'])->name('ventas');
        Route::get('/productos', [ReportController::class, 'productos'])->name('productos');
        Route::get('/clientes', [ReportController::class, 'clientes'])->name('clientes');
        Route::get('/produccion-semanal', [ReportController::class, 'produccionSemanal'])->name('produccion-semanal');
        Route::get('/comparacion-semanal', [ReportController::class, 'comparacionSemanal'])->name('comparacion-semanal');
        Route::get('/conciliacion', [ReportController::class, 'conciliacion'])->name('conciliacion');
    });

    // Rutas para el CRUD de Clientes
    Route::resource('clientes', ClienteController::class);

    // Rutas para el CRUD de Insumos
    Route::resource('insumos', InsumoController::class);

    // Rutas para el CRUD de Pedidos
    Route::resource('pedidos', PedidoController::class);

    // Rutas específicas para pedidos
    Route::post('/pedidos/{pedido}/cancelar', [PedidoController::class, 'cancelar'])->name('pedidos.cancelar');
    Route::post('/pedidos/{pedido}/cambiar-estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar-estado');
    Route::delete('/pedidos/{pedido}/archivos/{archivo}', [PedidoController::class, 'destroyArchivo'])->name('pedidos.archivos.destroy');

    // Rutas para gestión de ítems de pedidos
    Route::prefix('pedidos/{pedido}')->group(function () {
        Route::get('/items/create', [PedidoItemController::class, 'create'])->name('pedidos.items.create');
        Route::post('/items', [PedidoItemController::class, 'store'])->name('pedidos.items.store');
        Route::get('/items/{item}/edit', [PedidoItemController::class, 'edit'])->name('pedidos.items.edit');
        Route::put('/items/{item}', [PedidoItemController::class, 'update'])->name('pedidos.items.update');
        Route::delete('/items/{item}', [PedidoItemController::class, 'destroy'])->name('pedidos.items.destroy');
    });

    // Rutas AJAX para cálculos
    Route::get('/api/maquinas', [PedidoItemController::class, 'getMaquinas'])->name('api.maquinas');
    Route::post('/api/calcular-precio', [PedidoItemController::class, 'calcularPrecio'])->name('api.calcular-precio');

    // Rutas para gestión de máquinas
    Route::resource('maquinas', MaquinaController::class);
    Route::resource('productos', ProductoController::class);
    Route::get('productos/categoria/{categoria}', [ProductoController::class, 'getByCategoria'])->name('productos.categoria');
    
    // API para búsqueda de clientes
    Route::get('/api/clientes/buscar', [ClienteController::class, 'buscar'])->name('api.clientes.buscar');

    // Rutas de gestión (solo administradores)
    Route::prefix('gestion')->name('gestion.')->middleware('role:admin')->group(function () {
        Route::get('/usuarios', [GestionController::class, 'usuarios'])->name('usuarios');
        Route::post('/usuarios', [GestionController::class, 'crearUsuario'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}', [GestionController::class, 'actualizarUsuario'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [GestionController::class, 'eliminarUsuario'])->name('usuarios.destroy');
        
        Route::get('/pedidos-cancelados', [GestionController::class, 'pedidosCancelados'])->name('pedidos-cancelados');
        Route::delete('/pedidos-cancelados/{pedido}', [GestionController::class, 'eliminarPedidoCancelado'])->name('pedidos-cancelados.destroy');
        
        Route::get('/contadores', [GestionController::class, 'contadores'])->name('contadores');
    });
});