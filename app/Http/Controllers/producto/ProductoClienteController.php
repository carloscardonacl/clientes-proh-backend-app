<?php

namespace App\Http\Controllers\Producto;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoClienteController extends Controller
{
    //
    public function index($idCliente)
    {
           
        try {
          
            $condicion = $this->getCondiciones();
            $cliente = Cliente::find($idCliente);

            if(!$cliente) throw new Exception("El cliente ingresado no existe", 401);
            
            $q = 'SELECT P.Nombre_Comercial, P.Codigo_Cum,
            P.Imagen,
            S.Nombre AS Subcategoria,
            IF(CONCAT( P.Nombre_Comercial," ",P.Cantidad, " ",P.Unidad_Medida, " (",P.Principio_Activo, " ",
                    P.Presentacion, " ",
                    P.Concentracion, ") " )="" OR CONCAT( P.Nombre_Comercial," ", P.Cantidad," ",
                    P.Unidad_Medida ," (",P.Principio_Activo, " ",
                    P.Presentacion, " ",
                    P.Concentracion, ") "
                   ) IS NULL, CONCAT(P.Nombre_Comercial), CONCAT( P.Nombre_Comercial," ", P.Cantidad," ",
                    P.Unidad_Medida, " (",P.Principio_Activo, " ",
                    P.Presentacion, " ",
                    P.Concentracion,") " )) as Nombre, 
            P.Nombre_Comercial, P.Laboratorio_Comercial, P.Laboratorio_Generico, P.Id_Producto, P.Embalaje, P.Cantidad_Presentacion, 
            
            ( SELECT Precio FROM Producto_Lista_Ganancia PLG
                        INNER JOIN Lista_Ganancia LG ON LG.Id_Lista_Ganancia = PLG.Id_Lista_Ganancia 
                        WHERE LG.Id_Lista_Ganancia = '.$cliente->Id_Lista_Ganancia.' AND
                        PLG.Cum = P.Codigo_Cum
                        ) AS Precio_Orden,  
            
           /*  IFNULL((SELECT Costo_Promedio  FROM Costo_Promedio WHERE Id_Producto = P.Id_Producto),"0") AS Costo, */
            
            (SELECT AR.Id_Proveedor
            
            FROM Producto_Acta_Recepcion PAR 
            INNER JOIN Acta_Recepcion AR ON PAR.Id_Acta_Recepcion=AR.Id_Acta_Recepcion
            
            WHERE PAR.Id_Producto = P.Id_Producto AND (AR.Estado = "Aprobada" OR AR.Estado = "Acomodada")
            Order BY AR.Fecha_Creacion DESC LIMIT 1 ) AS Proveedor,
            
            IF(P.Gravado = "Si" , 19 , 0 ) AS Impuesto,
            0 AS Total,
            1 AS Cantidad,
            P.Cantidad_Presentacion,
            0 AS Cantidad_Remision
            
            FROM Producto P
            INNER JOIN Subcategoria S ON S.Id_Subcategoria = P.Id_Subcategoria
            WHERE
            P.Codigo_Barras IS NOT NULL AND P.Estado="Activo" AND P.Codigo_Barras !="" AND
            (P.Embalaje NOT LIKE "MUESTRA MEDICA%" OR P.Embalaje IS NULL OR P.Embalaje="" )'.$condicion .' 
            GROUP BY P.Id_Producto
            HAVING Precio_Orden AND Proveedor IS NOT NULL';
           
           $res = ['Data'=>DB::select($q),'Cod'=>'ok','Message'=>'Operación extosa'];
           return response()->json($res,200); 

        } catch (\Throwable $th) {
       
            $res = ['Data'=>[],'Cod'=>'error','Message'=>$th->getMessage()];
            return response()->json($res,404);
        }

    }

    public function getCondiciones()
    {
        $condicion = '';     
        if ( Request()->nom  && Request()->nom != '') {
            $condicion .= ' AND (P.Principio_Activo LIKE "%'.Request()->nom.'%"
                         OR P.Presentacion LIKE "%'.Request()->nom.'" OR P.Concentracion LIKE
                          "%'.Request()->nom.'%" OR P.Nombre_Comercial LIKE "%'.Request()->nom.'%" 
                          OR P.Cantidad LIKE "%'.Request()->nom.'%" OR P.Unidad_Medida LIKE "%'.Request()->nom.'%")';
        }
        if (Request()->lab_com && Request()->lab_com) {
            $condicion .= " AND P.Laboratorio_Comercial LIKE '%Request()->lab_com%'";
        }

        if (Request()->lab_gen && Request()->lab_gen) {
            $condicion .= " AND P.Laboratorio_Generico LIKE '%Request()->lab_gen%'";
        }
        if (Request()->cum && Request()->cum) {
            $condicion .= " AND P.Codigo_Cum LIKE '%Request()->cum%'";
        }
        return $condicion;
    }

}
