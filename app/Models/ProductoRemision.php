<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoRemision extends Model
{
    use HasFactory;
    protected $table = "Producto_Remision";
    protected $primaryKey = "Id_Producto_Remision";

    public $timestamps = false;
}
