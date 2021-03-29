<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCupoView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::select($this->dropView());
    }

    private function createView(): string
    {
        return "CREATE VIEW cupo_view AS SELECT
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
        Cliente C ON C.Id_Cliente = MC.Nit;";
    }

    private function dropView(): string
    {
        return  "DROP VIEW IF EXISTS `cupo_view`;";
    }
}
