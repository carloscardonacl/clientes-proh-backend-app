<?php

namespace App\Http\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agentes_Cliente;
use App\Models\Auth;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;


class AuthController extends Controller
{

    public function login()
    {

        $indentificacion = request('Identificacion_Funcionario');
        $password = md5(request('Password'));

        $credenciales =  Agentes_Cliente::where('Identificacion', $indentificacion)
            ->where('Password', "$password")
            ->where('Estado', 'Activo')
            ->with('Cliente')
            ->first();

        if ($credenciales) {
            $data = Auth::encode($credenciales);
            return response()->json(['Data' => $data, 'Cod' => 'ok', 'Message' => 'Autenticación extosa'], 200);
        } else {
            $res = ['Cod' => 'error', 'Message' => 'Error de autenticación'];
            return response()->json($res, 400);
        }
    }
}
