<?php 
namespace App\Services;

use App\Models\Configuracion;
use App\Models\Remision;
use Illuminate\Support\Facades\DB;

class RemisionServices{

    static function armarRem( $grupos , array &$productos ){

        foreach ($grupos as $KGrupo => $grupo) {
         
            foreach ($productos as $KProducto => $producto) {
                if ($producto['Cantidad_Remision'] < $producto['Cantidad'] ) {
                    $inventarios = self::getInventario($producto['Id_Producto'] , $grupo['Id_Grupo_Estiba']);
                   // var_dump($inventarios);
                    foreach ( $inventarios as $KInventario => $inventario ) {
                        
                        if ( $producto['Cantidad_Remision'] < $producto['Cantidad'] ) {
                            $cantidad = 0;
                            if( $inventario['Cantidad'] > ( $productos[$KProducto]['Cantidad'] - $productos[$KProducto]['Cantidad_Remision'] ) ){
                                $cantidad = $productos[$KProducto]['Cantidad'] - $productos[$KProducto]['Cantidad_Remision'];
                                
                            }else{
                                $cantidad = $inventario['Cantidad'];
                            }
                            $productos[$KProducto]['Cantidad_Remision'] += $cantidad;

                            $inventarios[$KInventario]['Cantidad'] = $cantidad;
                            $inventarios[$KInventario]['Costo'] =  $productos[$KProducto]['Costo'];
                            $inventarios[$KInventario]['Precio'] =  $productos[$KProducto]['Precio_Orden'];
                            $inventarios[$KInventario]['Impuesto'] =  $productos[$KProducto]['Impuesto'];

                            //$productoTemporal[] = $inventarios[$KInventario];
                            $grupos[$KGrupo]['Productos'][]  = $inventarios[$KInventario];
                        }else{

                            break;
                        }
                    }
                // if($productoTemporal) $grupos[$KGrupo]['Productos'][] = $productoTemporal;
                }
            }
        }

        $gtemp = array_filter($grupos, function ($grupo)
        {
           return array_key_exists( 'Productos',  $grupo );
        });

        $grupos =  array_values($gtemp);
    
        foreach ($grupos as $key => $value) {
            $grupos[$key]['Totales'] = self::getTotales($value['Productos']); 
        } 

        return $grupos;
        
    } 


    static function getTotales($productos){
        $Costo = 0;
        $Subtotal = 0;
        $Impuesto = 0;

        foreach ($productos as $key => $prod) {
            $Costo += $prod['Costo'] * $prod['Cantidad'];
            $Subtotal += $prod['Precio'] * $prod['Cantidad'];
            $Impuesto += $prod['Impuesto'] * $prod['Cantidad'];
        }
        return ['Costo'=>$Costo, 'Subtotal'=>$Subtotal, 'Impuesto'=>$Impuesto];
    }

    static function getInventario($idProducto,$idGrupo){
      
        $query = 'SELECT  I.Id_Inventario_Nuevo , I.Codigo_CUM, I.Lote, I.Id_Producto, I.Fecha_Vencimiento,
                    ( I.Cantidad - (I.Cantidad_Seleccionada + I.Cantidad_Apartada) ) AS Cantidad
                    FROM Inventario_Nuevo I
                    INNER JOIN Estiba E ON E.Id_Estiba = I.Id_Estiba
                    INNER JOIN Grupo_Estiba G ON G.Id_Grupo_Estiba = E.Id_Grupo_Estiba 
                    WHERE I.Id_Producto = '.$idProducto.' AND G.Id_Grupo_Estiba = '.$idGrupo.' AND E.Id_Bodega_Nuevo = 1
                    HAVING Cantidad > 0
                    '
                    ;

        return  json_decode(json_encode(DB::select($query)),true);

    }

    static function armarCompra(&$productos)
    {
       
        $compra = [];
        foreach ($productos as $key => $prod) {
           
    
            if ($prod['Cantidad'] != $prod['Cantidad_Remision']) {
                $productos[$key]['Cantidad_Compra']   = $prod['Cantidad'] - $prod['Cantidad_Remision'];
    
                if (array_key_exists($prod['Proveedor'], $compra)) {
                    array_push($compra[$prod['Proveedor']]['Productos'], $productos[$key]);
                } else {
                    $compra[$prod['Proveedor']] = [];
                    $compra[$prod['Proveedor']]['Productos'][] = $productos[$key];
                }
            }else{
                $productos[$key]['Cantidad_Compra']  = 0;
            }
        }
        return $compra;
    }

    /* function crearRem($grupos , $cabecera){
        global $codigos_rem;
        foreach ($grupos as $keyGrupo => $grupo) {
             # code...
   
             $item_remision=self::GetLongitudRemision();
             $remisiones=array_chunk($grupo['Productos'],$item_remision);
        
             foreach ($remisiones as  $value) {
                  $id_remision=SaveEncabezado($cabecera, $grupo);       
                  SaveProductoRemision($id_remision,$value);
             }
        }
        return $codigos_rem;
   }
 */

   static function GetLongitudRemision(){
        $config=Configuracion::find(1);
        return $config->Max_Item_Remision;
    }

    /* 
function SaveEncabezado($cabecera,$grupo ){
    global  $bodega ;
    
    $configuracion = new Configuracion();
    $oItem = new complex("Remision","Id_Remision");
    $oItem->Fecha = date("Y-m-d H:i:s");
    $oItem->Meses = 1;
    $oItem->Tipo = 'Cliente';
    $oItem->Prioridad = 1;
    $oItem->Meses = 4;
    $oItem->Nombre_Destino = $cabecera['cliente']['Nombre'];
    $oItem->Nombre_Origen = $bodega['Nombre'];
    $oItem->Identificacion_Funcionario = $cabecera['Identificacion_Funcionario'];
    $oItem->Observaciones = $cabecera['observaciones'];
    $oItem->Tipo_Origen = 'Bodega';
    $oItem->Tipo_Destino = 'Cliente';
    $oItem->Id_Origen = $bodega['Id_Bodega_Nuevo'];
    $oItem->Id_Destino = $cabecera['cliente']['Id_Cliente'];
    $oItem->Estado = 'Pendiente';
    $oItem->Estado_Alistamiento = 0;
    $oItem->Id_Lista = 1;
    $oItem->Costo_Remision = $grupo['Totales']['Costo'];
    $oItem->Subtotal_Remision = $grupo['Totales']['Subtotal'];
    $oItem->Descuento_Remision = 0;
    $oItem->Impuesto_Remision = $grupo['Totales']['Impuesto'];
    $oItem->Entrega_Pendientes = 'No';
    $oItem->Id_Grupo_Estiba  = $grupo['Id_Grupo_Estiba'];
    $oItem->Id_Orden_Pedido  = $cabecera['Id_Orden_Pedido'];

    $oItem->Codigo=$configuracion->getConsecutivo('Remision','Remision');
    $oItem->save();
    $id_remision = $oItem->getId();
    unset($oItem);

  //  $qr = generarqr('remision',$id_remision,'/IMAGENES/QR/');
  //  $oItem = new complex("Remision","Id_Remision",$id_remision);
  //  $oItem->Codigo_Qr=$qr;
  //  $oItem->save();
   // unset($oItem); 
    unset($configuracion);
    return $id_remision;

} */

}
