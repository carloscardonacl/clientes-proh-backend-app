<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrdenPedido\OrdenPedidoController;
use App\Http\Controllers\OrdenPedido\PreOrdenPedidoController;
use App\Http\Controllers\producto\ProductoClienteController;
use App\Http\Auth\AuthController;
use App\Http\Controllers\Agente\AgenteClienteController;
use App\Http\Controllers\Cupo\FinanzaController;
use Illuminate\Support\Facades\DB;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['jwt', 'getRequest'])->group(function () {

    Route::get('productos/cliente/{id}', [ProductoClienteController::class, 'index']);

    Route::post('pre-orden', [PreOrdenPedidoController::class, 'preOrden']);

    Route::resource('orden-pedido', OrdenPedidoController::class)->names('orden-pedido');

    //Agentes clientes routes
    Route::resource('agentes-clientes', AgenteClienteController::class)->names('agentes-clientes');
    
    //cartera cliente
    Route::get('cliente-cupo', [FinanzaController::class, 'getCupo'])->name('cliente-cupo');
    Route::get('cliente-cartera', [FinanzaController::class, 'getCartera'])->name('cliente-cartera');

    //Agentes clientes routes
    
});

Route::post('/login', [AuthController::class, 'login']);
