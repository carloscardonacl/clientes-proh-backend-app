<?php

namespace App\Http\Controllers\OrdenPedido;

use App\Http\Controllers\Controller;
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
            if($tipoSeleccion == 'disponibles'){
                
                $productos = Request()->productos;
               
                
                if(!$productos) throw new Exception("Error Processing Request", 1);

                if (!$this->validarInventario($productos)) {
                
                  throw new  Exception("Error Processing Request", 1);
                }

            }

            if($tipoSeleccion == 'completo'){


            }


            $pendietes = Request()->pendientes;
            

           

/*             if(!$Remisiones){
                 throw new Exception("Las remisiones son obligatorias", 1);
                
            } */

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function validarInventario($productos){
       
       dd( GetBodegaOrden() );
        /* foreach ($productos as $key => $producto) {
            # code...
            $q='SELECT SUM(Cantidad) FROM '
        } */
    }
}
