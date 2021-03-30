<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoOrdenPedido extends Model
{
    use HasFactory;
    protected $table = "Producto_Orden_Pedido";
    protected $primaryKey = "Id_Producto_Orden_Pedido";
    public $timestamps = false;
   
    public function productos(){
        return $this->belongsTo(Producto::class,"Id_Producto","Id_Producto");
    }

}
