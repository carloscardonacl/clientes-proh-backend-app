<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenPedido extends Model
{
    use HasFactory;
    protected $table = "Orden_Pedido";
    protected $primaryKey = "Id_Orden_Pedido";

    public $timestamps = false;
    
   
    public function productosOrden(){
        return $this->hasMany(Producto_Orden_Pedido::class,"Id_Orden_Pedido","Id_Orden_Pedido");
    }
    public function remisiones(){
        return $this->hasMany(Remision::class,"Id_Orden_Pedido","Id_Orden_Pedido");
    }
    
    public function funcionario(){
        return $this->belongsTo(Funcionario::class,"Identificacion_Funcionario","Identificacion_Funcionario");
    }
    
    public function agenteCliente(){
        return $this->belongsTo(Agentes_Cliente::class,"Id_Agentes_Cliente","Id_Agentes_Cliente");
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class,"Id_Cliente","Id_Cliente");
    }

}
