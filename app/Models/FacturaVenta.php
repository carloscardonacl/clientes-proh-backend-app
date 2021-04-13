<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaVenta extends Model
{
    use HasFactory;
    protected $table = 'Factura_Venta';
    protected $primaryKey = 'Id_Factura_Venta';
    public $timestamps = false;
}
