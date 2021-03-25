<?php

namespace App\Http\Controllers\OrdenPedido;

use App\Http\Controllers\Controller;
use App\Models\GrupoEstiba;
use App\Services\RemisionServices;
use Illuminate\Http\Request;

class PreOrdenPedidoController extends Controller
{
    //
    
    public function preOrden(){
        $data = Request()->data;
        $data = json_decode($data,true);
        $productos = $data['productos'];
    
        $cabecera = $data['cabecera'];

        $grupos = GrupoEstiba::all()->toArray();

        RemisionServices::armarRem( $grupos, $productos);
     
        $disponibles = []; 
        $pendientes = []; 
        foreach ($productos as $key => $prod) {
            # code...
            // dd($productos[$key]['Cantidad_Remision']);
            $cantidadCompra = ( $productos[$key]['Cantidad'] - $productos[$key]['Cantidad_Remision'] ) ;
            
            
            if(  $productos[$key]['Cantidad_Remision'] ){
                $productos[$key]['Cantidad_Solicitada'] =  $productos[$key]['Cantidad'];
                $productos[$key]['Cantidad'] =  $productos[$key]['Cantidad_Remision'];
                $productos[$key]['Cantidad_Remision'] = 0;
                $disponibles[] = $productos[$key];
            }
            if( $cantidadCompra > 0 ){
                $productos[$key]['Cantidad_Solicitada'] =  $productos[$key]['Cantidad'];
                $productos[$key]['Cantidad'] =  $cantidadCompra;
                $productos[$key]['Cantidad_Remision'] = 0;
                $pendientes[] = $productos[$key];
            }
        }

        $res = [  'Data'=> [ 'disponibles' => $disponibles, 'pendientes' => $pendientes],
                    'Cod'=>'ok',
                    'Message'=>'Operación extosa'
                ];

     return response()->json($res,200); 
        
    }
   
    public function armarRem(){
        
    }

}

/* 
