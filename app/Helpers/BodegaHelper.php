<?php

function GetBodegaOrden(){
    $bodega = \App\Models\BodegaNuevo::where('Orden_Pedido','1')->first();
    return $bodega->Id_Bodega_Nuevo;
}