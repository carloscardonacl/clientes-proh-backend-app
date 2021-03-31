<?php

use Illuminate\Support\Facades\File;

/**
 * Destroy file.
 *
 * @param  string $filename
 * @param  string  $folder
 * @return void
 */

if (!function_exists('destroyFile')) {

    function destroyFile($filename, $folder)
    {
        try {
            File::delete(public_path() . "/$folder/" . $filename);
            return;
        } catch (\Throwable $th) {
            return response($th->getMessage());
        }
    }
}


/**
 * upload  file.
 *
 * @param  \Illuminate\Http\Request 
 * @param  string  $folder
 * @return string $filename name from file for save on model
 */

if (!function_exists('getNameFile')) {
    function getNameFile($request, $folder)
    {
        try {
            $filename =  time() . '.' . $request->file('Avatar')->getClientOriginalExtension();
            $request->file('Avatar')->move(public_path() . "/$folder", $filename);
            return  $filename;
        } catch (\Throwable $th) {
            return response($th->getMessage());
        }
    }
}


// return DB::select("SELECT
//                     DATE(MC.Fecha_Movimiento) AS Fecha_Documento,
//                     MC.Documento AS Codigo,
//                     0 AS Exenta,
//                     0 AS Gravada,
//                     0 AS Iva,
//                     (CASE PC.Naturaleza
//                     WHEN 'C' THEN (SUM(MC.Haber))
//                     ELSE (SUM(MC.Debe))
//                     END) AS Total_Compra,
//                     (CASE PC.Naturaleza
//                     WHEN 'C' THEN (SUM(MC.Haber) - SUM(MC.Debe))
//                     ELSE (SUM(MC.Debe) - SUM(MC.Haber))
//                     END) AS Neto_Factura,
//                     PC.Naturaleza AS Nat,
//                     DATE_ADD(DATE(MC.Fecha_Movimiento), INTERVAL IF(C.Condicion_Pago IN (0,1),0,C.Condicion_Pago) DAY) AS Fecha_Vencimiento,
//                     IF(C.Condicion_Pago IN (0,1),0,C.Condicion_Pago) AS Condicion_Pago,
//                     IF(C.Condicion_Pago > 1, IF(DATEDIFF(CURDATE(), DATE(MC.Fecha_Movimiento)) > C.Condicion_Pago, DATEDIFF(CURDATE(), DATE(MC.Fecha_Movimiento)) - C.Condicion_Pago, 0), 0) AS Dias_Mora
//                     FROM
//                     Movimiento_Contable MC
//                     INNER JOIN
//                     Plan_Cuentas PC ON MC.Id_Plan_Cuenta = PC.Id_Plan_Cuentas
//                     INNER JOIN
//                     Cliente C ON C.Id_Cliente = MC.Nit
//                     WHERE
//                     MC.Estado != 'Anulado'
//                     AND Id_Plan_Cuenta = 57
//                     AND MC.Nit = 901097473
//                     GROUP BY MC.Id_Plan_Cuenta , MC.Documento
//                     HAVING Neto_Factura != 0
//                     ORDER BY MC.Fecha_Movimiento DESC
// ");