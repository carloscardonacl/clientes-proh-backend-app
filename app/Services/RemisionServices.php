<?php

namespace App\Services;

use App\Models\Configuracion;
use App\Models\ProductoRemision;
use App\Models\Remision;
use Illuminate\Support\Facades\DB;

class RemisionServices
{

    static function armarRem($grupos, array &$productos)
    {

        foreach ($grupos as $KGrupo => $grupo) {

            foreach ($productos as $KProducto => $producto) {
                if ($producto['Cantidad_Remision'] < $producto['Cantidad']) {
                    $inventarios = self::getInventario($producto['Id_Producto'], $grupo['Id_Grupo_Estiba']);
                    // var_dump($inventarios);
                    foreach ($inventarios as $KInventario => $inventario) {

                        if ($producto['Cantidad_Remision'] < $producto['Cantidad']) {
                            $cantidad = 0;
                            if ($inventario['Cantidad'] > ($productos[$KProducto]['Cantidad'] - $productos[$KProducto]['Cantidad_Remision'])) {
                                $cantidad = $productos[$KProducto]['Cantidad'] - $productos[$KProducto]['Cantidad_Remision'];
                            } else {
                                $cantidad = $inventario['Cantidad'];
                            }
                            $productos[$KProducto]['Cantidad_Remision'] += $cantidad;

                            $inventarios[$KInventario]['Cantidad'] = $cantidad;
                            $inventarios[$KInventario]['Costo'] =  $productos[$KProducto]['Costo'];
                            $inventarios[$KInventario]['Precio'] =  $productos[$KProducto]['Precio_Orden'];
                            $inventarios[$KInventario]['Impuesto'] =  $productos[$KProducto]['Impuesto'];

                            //$productoTemporal[] = $inventarios[$KInventario];
                            $grupos[$KGrupo]['Productos'][]  = $inventarios[$KInventario];
                        } else {

                            break;
                        }
                    }
                    // if($productoTemporal) $grupos[$KGrupo]['Productos'][] = $productoTemporal;
                }
            }
        }

        $gtemp = array_filter($grupos, function ($grupo) {
            return array_key_exists('Productos',  $grupo);
        });

        $grupos =  array_values($gtemp);

        foreach ($grupos as $key => $value) {
            $grupos[$key]['Totales'] = self::getTotales($value['Productos']);
        }

        return $grupos;
    }


    static function getTotales($productos)
    {
        $Costo = 0;
        $Subtotal = 0;
        $Impuesto = 0;

        foreach ($productos as $key => $prod) {
            $Costo += $prod['Costo'] * $prod['Cantidad'];
            $Subtotal += $prod['Precio'] * $prod['Cantidad'];
            $Impuesto += $prod['Impuesto'] * $prod['Cantidad'];
        }
        return ['Costo' => $Costo, 'Subtotal' => $Subtotal, 'Impuesto' => $Impuesto];
    }

    static function getInventario($idProducto, $idGrupo)
    {
        $idBodega = GetIdBodegaOrden();
        $query = 'SELECT  I.Id_Inventario_Nuevo , I.Codigo_CUM, I.Lote, I.Id_Producto, I.Fecha_Vencimiento,
                    ( I.Cantidad - (I.Cantidad_Seleccionada + I.Cantidad_Apartada) ) AS Cantidad
                    FROM Inventario_Nuevo I
                    INNER JOIN Estiba E ON E.Id_Estiba = I.Id_Estiba
                    INNER JOIN Grupo_Estiba G ON G.Id_Grupo_Estiba = E.Id_Grupo_Estiba 
                    WHERE I.Id_Producto = ' . $idProducto . ' AND G.Id_Grupo_Estiba = ' . $idGrupo . ' AND E.Id_Bodega_Nuevo = ' . $idBodega . '
                    HAVING Cantidad > 0
                    ';

        return  json_decode(json_encode(DB::select($query)), true);
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
            } else {
                $productos[$key]['Cantidad_Compra']  = 0;
            }
        }
        return $compra;
    }

    static function crearRem($grupos, $cabecera)
    {
       $codigos_rem = '';
        foreach ($grupos as $keyGrupo => $grupo) {
            # code...

            $item_remision = self::GetLongitudRemision();
            $remisiones = array_chunk($grupo['Productos'], $item_remision);

            foreach ($remisiones as  $value) {
                $id_remision = self::SaveEncabezado($cabecera, $grupo);
                self::SaveProductoRemision($id_remision, $value);
            }
        }
        return $codigos_rem;
    }


    static function GetLongitudRemision()
    {
        $config = Configuracion::find(1);
        return $config->Max_Item_Remision;
    }


    static function SaveEncabezado($cabecera, $grupo)
    {
    
        $bodega = GetBodegaOrden();
      //  dd($bodega);
        $rem = new Remision();
        $rem->Fecha = date("Y-m-d H:i:s");
        $rem->Meses = 1;
        $rem->Tipo = 'Cliente';
        $rem->Prioridad = 1;
        $rem->Meses = 4;
        $rem->Nombre_Destino = $cabecera['cliente']['Nombre'];
        $rem->Nombre_Origen = $bodega->Nombre;
        $rem->Id_Origen = $bodega->Id_Bodega_Nuevo;
        $rem->Identificacion_Funcionario = $cabecera['Identificacion_Funcionario'];
        $rem->Observaciones = $cabecera['observaciones'];
        $rem->Tipo_Origen = 'Bodega';
        $rem->Tipo_Destino = 'Cliente';
        $rem->Id_Destino = $cabecera['cliente']['Id_Cliente'];
        $rem->Estado = 'Validacion';
        $rem->Estado_Alistamiento = 0;
        $rem->Id_Lista = 1;
        $rem->Costo_Remision = $grupo['Totales']['Costo'];
        $rem->Subtotal_Remision = $grupo['Totales']['Subtotal'];
        $rem->Descuento_Remision = 0;
        $rem->Impuesto_Remision = $grupo['Totales']['Impuesto'];
        $rem->Entrega_Pendientes = 'No';
        $rem->Id_Grupo_Estiba  = $grupo['Id_Grupo_Estiba'];
        $rem->Id_Orden_Pedido  = $cabecera['Id_Orden_Pedido'];

        $config = Configuracion::find(1);
        $rem->Codigo =  $config->getConsecutivo('Remision','Remision');
    
        $rem->save();

        //unset($oItem);

        //  $qr = generarqr('remision',$id_remision,'/IMAGENES/QR/');
        //  $oItem = new complex("Remision","Id_Remision",$id_remision);
        //  $oItem->Codigo_Qr=$qr;
        //  $oItem->save();
        // unset($oItem); 
        //unset($configuracion);
        return $rem->Id_Remision;
    }

    static function SaveProductoRemision($id_remision,$productos){      
    
         foreach ($productos as $producto) {
             
              $p= new ProductoRemision();
              $p->Id_Inventario_Nuevo = $producto['Id_Inventario_Nuevo'];
              $p->Id_Producto = $producto['Id_Producto'];
              $p->Lote = $producto['Lote'];
              $p->Cantidad = $producto['Cantidad'];
              $p->Fecha_Vencimiento = $producto['Fecha_Vencimiento'];
              
              $subtotal=($producto['Cantidad']*$producto['Precio']);
    
              $p->Subtotal=number_format($subtotal,2,".","");               
              $p->Total_Descuento= number_format($subtotal,2,".","");      
    
              $subtotal=($producto['Cantidad']*$producto['Precio'])*($producto['Impuesto']/100);
              $p->Total_Impuesto =number_format($subtotal,2,".","");                  
    
              $p->Impuesto  = $producto['Impuesto'];
              $p->Descuento  = 0 ;
              $p->Cantidad_Total  = $producto['Cantidad'];
              
              $p->Id_Remision=$id_remision;
              //unset($p['Cantidad']);
              /*   $oItem->Cantidad=$p['Cantidad_Seleccionada']; */
              $p->Precio=number_format($producto['Precio'],2,".","");
              $p->Costo=number_format((int)$producto['Costo'],2,".","");
              
              $p->save();
              unset($p);
            
         }
    
        // GuardarActividadRemision($id_remision);
    }
}
