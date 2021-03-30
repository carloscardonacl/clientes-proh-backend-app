<?php 
namespace App\Services;

use App\Models\OrdenPedido;
use App\Models\ProductoOrdenPedido;
use Illuminate\Support\Facades\DB;

class OrdenPedidoServices{
    
    static function guardarCabecera($cabecera)
    {
        $Orden = new OrdenPedido();
        $Orden->Id_Cliente = $cabecera['cliente']['Id_Cliente'];
        $Orden->Id_Agentes_Cliente = $cabecera['agente']['Id_Agentes_Cliente'];
        $Orden->Fecha_Probable_Entrega = $cabecera['fecha_probable_entrega'];
        $Orden->Identificacion_Funcionario = $cabecera['Identificacion_Funcionario'];
        $Orden->Observaciones = $cabecera['observaciones'];
      //  $Orden->Archivo_Compra_Cliente = $cabecera['Archivo_Compra_Cliente'];
       // $Orden->Orden_Compra_Cliente = $cabecera['ordenCompra'];
        $Orden->Estado = 'Activa';
        $Orden->save();
        return $Orden->Id_Orden_Pedido;
    }

    static function guardarProductos($idOrden,$productos){
        foreach ($productos as $producto) {
            $productoPrecompra = new ProductoOrdenPedido();
            $productoPrecompra->Id_Orden_Pedido = $idOrden;
            $productoPrecompra->Id_Producto     = $producto['Id_Producto'];
            $productoPrecompra->Cantidad        = $producto['Cantidad'];
            $productoPrecompra->Precio_Orden    = number_format($producto['Precio_Orden'],2,".","");
            $productoPrecompra->Impuesto        = $producto['Impuesto'];
            $productoPrecompra->Precio          = number_format($producto['Precio'],2,".","");
            $productoPrecompra->Costo           = number_format($producto['Costo'],2,".","");
            $productoPrecompra->Id_Proveedor    = $producto['Proveedor'];
            $productoPrecompra->Cantidad_Compra = $producto['Cantidad_Compra'];
            $productoPrecompra->save();
        }
    }



}