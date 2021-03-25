<?php

namespace App\Models;

use App\Scopes\GrupoEstibaScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoEstiba extends Model
{
    use HasFactory;
    protected $table = "Grupo_Estiba";
    protected $primaryKey = "Id_Grupo_Estiba";

       protected static function booted()
    {
        static::addGlobalScope(new GrupoEstibaScope);
    }
}
