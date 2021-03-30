<?php

function GetIdBodegaOrden(){
    $bodega = \App\Models\BodegaNuevo::where('Orden_Pedido','1')->first();
    return $bodega->Id_Bodega_Nuevo;
}
function GetBodegaOrden(){
   
    return  \App\Models\BodegaNuevo::where('Orden_Pedido','1')->first();
}