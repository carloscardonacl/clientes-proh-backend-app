<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrdenPedido\OrdenPedidoController;
use App\Http\Controllers\OrdenPedido\PreOrdenPedidoController;
use App\Http\Controllers\Producto\ProductoClienteController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('productos/cliente/{id}',[ProductoClienteController::class,'index']);



Route::post('pre-orden',[PreOrdenPedidoController::class,'preOrden']);

Route::post('orden-pedido',[OrdenPedidoController::class,'store']);