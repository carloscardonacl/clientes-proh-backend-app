<?php

namespace App\Http\Controllers\OrdenPedido;

use App\Http\Controllers\Controller;
use App\Models\Auth;
use App\Models\GrupoEstiba;
use App\Models\OrdenPedido;
use App\Services\OrdenPedidoServices;
use App\Services\PreCompraServices;
use App\Services\RemisionServices;
use Exception;
use Illuminate\Http\Request;

class OrdenPedidoController extends Controller
{
    //
    public function store()
    {
        # code...

        try {
            //code...
            $tipoSeleccion = Request()->tipoSeleccion;
         

            $data = Request()->data;
            $data = json_decode($data,true);
            $productos = $data['productos'];
        
            $cabecera = $data['cabecera'];

            $grupos = GrupoEstiba::where('Orden_Pedido','Si')->get()->toArray();
            
            $grupos = RemisionServices::armarRem($grupos,$productos);
           
            $compra = RemisionServices::armarCompra($productos);
            //dd($productos);exit;
           // dd([$grupos,$compra]);exit;
           // dd($compra);

            if($tipoSeleccion == 'disponible'){       
               // dd($compra);     
                if($compra) throw new Exception("Advertencia las cantidades cambiaron", 1);      
                
                $idOrden = OrdenPedidoServices::guardarCabecera($cabecera);
                //crear rem
                $cabecera['Id_Orden_Pedido'] = $idOrden;
                $codsRem = RemisionServices::crearRem($grupos, $cabecera);
                OrdenPedidoServices::guardarProductos($idOrden,$productos);

            }
            
            if($tipoSeleccion == 'completo'){
                
                $idOrden = OrdenPedidoServices::guardarCabecera($cabecera);
                
                //crear rem
                $cabecera['Id_Orden_Pedido'] = $idOrden;
                $codsRem = RemisionServices::crearRem($grupos, $cabecera);
                
                //guardar productos pedido
                OrdenPedidoServices::guardarProductos($idOrden,$productos);

                //guardar PreCompra 
                PreCompraServices::guardarPreCompra($cabecera,$compra,$idOrden);
              
               
            }
          
            echo json_encode(['text'=>"Orden de pedido realizado con éxito",'type'=>"success",'title'=>"Operación Exitosa"]);
           

        } catch (\Throwable $th) {
            //throw $th;
            echo json_encode(['text'=>$th->getMessage(),'type'=>"error",'title'=>"Ha ocurrido un error"] );
        }
    }

    public function index(){
        $idAgente = Auth::getUser()->Id_Agentes_Cliente;
        $idCliente = Auth::getUser()->Id_Cliente;
        
        $result = OrdenPedido::with([
                                'remisiones' => function($query) {
                                    $query->select('*');
                                }
                            ])
                            ->where('Id_Agentes_Cliente',$idAgente)
                            ->where('Id_Cliente',$idCliente)
                            ->orderBy('Id_Orden_Pedido', 'DESC')
                            ->get();  
       
        return response()->json(['Data' => $result, 'Cod' => 'ok', 'Message' => 'Operacion exitosa']);

    }
 
}
