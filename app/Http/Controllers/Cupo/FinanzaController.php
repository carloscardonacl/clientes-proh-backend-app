<?php

namespace App\Http\Controllers\Cupo;

use App\Http\Controllers\Controller;
use App\Models\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class FinanzaController extends Controller
{
    public function getCartera()
    {

        $limit = 10;
        $page = 1;

        if (request()->has('limit')) {
            $limit = request()->get('limit');
        }

        if (request()->has('page')) {
            $page =  request()->get('page');
        }

        $cliente = Auth::getUser()->Id_Cliente;

        $data =   DB::select("SELECT
                            DATE(MC.Fecha_Movimiento) AS Fecha_Documento,
                            MC.Documento AS Codigo,
                            0 AS Exenta,
                            0 AS Gravada,
                            0 AS Iva,
                            (CASE PC.Naturaleza
                            WHEN 'C' THEN (SUM(MC.Haber))
                            ELSE (SUM(MC.Debe))
                            END) AS Total_Compra,
                            (CASE PC.Naturaleza
                            WHEN 'C' THEN (SUM(MC.Haber) - SUM(MC.Debe))
                            ELSE (SUM(MC.Debe) - SUM(MC.Haber))
                            END) AS Neto_Factura,
                            PC.Naturaleza AS Nat,
                            DATE_ADD(DATE(MC.Fecha_Movimiento), INTERVAL IF(C.Condicion_Pago IN (0,1),0,C.Condicion_Pago) DAY) AS Fecha_Vencimiento,
                            IF(C.Condicion_Pago IN (0,1),0,C.Condicion_Pago) AS Condicion_Pago,
                            IF(C.Condicion_Pago > 1, IF(DATEDIFF(CURDATE(), DATE(MC.Fecha_Movimiento)) > C.Condicion_Pago, DATEDIFF(CURDATE(), DATE(MC.Fecha_Movimiento)) - C.Condicion_Pago, 0), 0) AS Dias_Mora
                            FROM
                            Movimiento_Contable MC
                            INNER JOIN
                            Plan_Cuentas PC ON MC.Id_Plan_Cuenta = PC.Id_Plan_Cuentas
                            INNER JOIN
                            Cliente C ON C.Id_Cliente = MC.Nit
                            WHERE
                            MC.Estado != 'Anulado'
                            AND Id_Plan_Cuenta = 57
                            AND MC.Nit = $cliente
                            GROUP BY MC.Id_Plan_Cuenta , MC.Documento
                            HAVING Neto_Factura != 0
                            ORDER BY MC.Fecha_Movimiento DESC
                            LIMIT $limit OFFSET $page
        ");


        $total  =  DB::select("SELECT Count(*) As Total From (SELECT
            (CASE PC.Naturaleza
            WHEN 'C' THEN (SUM(MC.Haber))
            ELSE (SUM(MC.Debe))
            END) AS Total_Compra,
            (CASE PC.Naturaleza
            WHEN 'C' THEN (SUM(MC.Haber) - SUM(MC.Debe))
            ELSE (SUM(MC.Debe) - SUM(MC.Haber))
            END) AS Neto_Factura
            FROM
            Movimiento_Contable MC
            INNER JOIN
            Plan_Cuentas PC ON MC.Id_Plan_Cuenta = PC.Id_Plan_Cuentas
            INNER JOIN
            Cliente C ON C.Id_Cliente = MC.Nit
            WHERE
            MC.Estado != 'Anulado'
            AND Id_Plan_Cuenta = 57
            AND MC.Nit = $cliente
            GROUP BY MC.Id_Plan_Cuenta , MC.Documento
            HAVING Neto_Factura != 0
            ORDER BY MC.Fecha_Movimiento DESC
        ) As Total 
    ")[0]->Total;

        $result         = new stdClass();
        $result->page   = $page;
        $result->limit    = $limit;
        $result->total    = $total;
        $result->data    = $data;

        return response()->json(['Data' => $result, 'Cod' => 'ok', 'Message' => 'Operacion exitosa']);
    }


    public function getCupo($limit = 10, $page = 1)
    {

        $cliente = Auth::getUser()->Id_Cliente;

        $data =   DB::select("SELECT 
        C.Cupo,
        R.Id_Cliente, 
        R.Nombre, 
        MAX(R.Dias_Mora) AS Dias_Mora, SUM(R.TOTAL) AS TOTAL FROM
        (SELECT MC.Id_PLan_Cuenta, C.Id_Cliente, C.Nombre, MC.Fecha_Movimiento, IF(C.Condicion_Pago > 1,
         IF(DATEDIFF(CURDATE(), DATE(MC.Fecha_Movimiento)) > C.Condicion_Pago, DATEDIFF(CURDATE(),
          DATE(MC.Fecha_Movimiento)) - C.Condicion_Pago, 0), 0) AS Dias_Mora,
            (CASE PC.Naturaleza
            WHEN 'C' THEN (SUM(MC.Haber) - SUM(MC.Debe))
            ELSE (SUM(MC.Debe) - SUM(MC.Haber))
            END) AS TOTAL
            FROM
            Movimiento_Contable MC
            INNER JOIN Plan_Cuentas PC ON MC.Id_Plan_Cuenta = PC.Id_Plan_Cuentas
            INNER JOIN Cliente C ON C.Id_Cliente = MC.Nit
            WHERE MC.Estado != 'Anulado'
            AND C.Id_Cliente  = $cliente
            AND Id_Plan_Cuenta = 57
            GROUP BY MC.Documento, C.Id_Cliente 
            HAVING TOTAL != 0
        ) R
        INNER JOIN Cliente C ON C.Id_Cliente = R.Id_Cliente
        GROUP BY R.Id_Plan_Cuenta, R.Id_Cliente
        ");


        return response()->json(['Data' => $data, 'Cod' => 'ok', 'Message' => 'Operacion exitosa']);
    }
}
