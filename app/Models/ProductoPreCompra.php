<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoPreCompra extends Model
{
    use HasFactory;
    protected $table = "Producto_Pre_Compra";
    protected $primaryKey = "Id_Producto_Pre_Compra";
    
}
