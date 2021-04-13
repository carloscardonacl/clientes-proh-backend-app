<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remision extends Model
{
    use HasFactory;

    use HasFactory;
    protected $table = "Remision";
    protected $primaryKey = "Id_Remision";

    public $timestamps = false;

    public function factura(){
        return $this->belongsTo(FacturaVenta::class,"Id_Factura_Venta","Id_Factura_Venta");
    }

}
