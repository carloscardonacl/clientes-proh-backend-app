<?php

namespace App\Models;

/* use App\Scopes\ProductoScope; */
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $table = "Producto";
    protected $primaryKey = "Id_Producto";

   /*  protected static function booted()
    {
        static::addGlobalScope(new ProductoScope);
    } */

}
