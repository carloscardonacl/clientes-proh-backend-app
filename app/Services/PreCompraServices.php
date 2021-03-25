<?php 
namespace App\Services;

use App\Models\PreCompra;
use App\Models\ProductoPreCompra;
use Illuminate\Support\Facades\DB;

class PreCompraServices{

    static function guardarPreCompra( $cabecera, $proveedores, $idOrden ){
        foreach ($proveedores as $key => $prov) {
            $preCompra= new PreCompra();
            $preCompra->Identificacion_Funcionario= $cabecera['Identificacion_Funcionario'];
            $preCompra->Id_Orden_Pedido = $cabecera['Id_Orden_Pedido'];
            $preCompra->Id_Proveedor = $key;
            $preCompra->Tipo = 'Orden_Pedido';
            $preCompra->Id_Orden_Pedido =  $idOrden;
            $preCompra->save();
            $id_pre_compra= $preCompra->Id_Pre_Compra;

            foreach ($prov["Productos"] as $item) {
                if( isset($item["Id_Producto"]) && $item["Id_Producto"]!='' ){
                    $producto= new ProductoPreCompra();
                    $producto->Id_Pre_Compra=$id_pre_compra;
                    $producto->Id_Producto = $item["Id_Producto"];
                    if($item["Cantidad_Compra"]==''){
                        $producto->Cantidad = 0;
                    }else{
                        $producto->Cantidad = $item["Cantidad_Compra"];
                    }
                    if($item["Costo"]==''){
                        $producto->Costo = 0;
                    }else{
                        $producto->Costo = $item["Costo"];
                    }
                    $producto->save();   
                }
            }
        }
    }


}