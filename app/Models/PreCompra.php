<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreCompra extends Model
{
    use HasFactory;
    protected $table = "Pre_Compra";
    protected $primaryKey = "Id_Pre_Compra";
    
}
