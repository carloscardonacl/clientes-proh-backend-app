<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;
    protected $table = 'Funcionario';
    protected $primaryKey = 'Identificacion_Funcionario';
    public $timestamps = false;
}
