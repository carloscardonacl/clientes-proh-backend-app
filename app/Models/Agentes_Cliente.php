<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agentes_Cliente extends Model
{
    use HasFactory;
    protected $table = 'agentes_cliente';
    protected $primaryKey = 'Id_Agentes_Cliente';
    public $timestamps = false;

    protected $fillable = [
        'Nombres',
        'Apellidos',
        'Identificacion',
        'Email',
        'Celular',
        'Telefono',
        'Password',
        'Imagen'
    ];

    protected $hidden = [
        'Password',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'Id_Cliente', 'Id_Cliente');
    }
}
