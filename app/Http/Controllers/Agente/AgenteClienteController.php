<?php

namespace App\Http\Controllers\Agente;

use App\Http\Controllers\Controller;
use App\Models\Agentes_Cliente;
use App\Models\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;


class AgenteClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $data = Agentes_Cliente::findOrfail(Auth::getUser()->Id_Agentes_Cliente);

            if (request()->hasFile('Avatar')) {
                destroyFile($data->Imagen, 'AVATARS_AGENTE');
                request()->merge(['Imagen' => getNameFile($request, 'AVATARS_AGENTE')]);
            }

            $data->update(request()->except('Password'));

            if (request()->has('Password') && request()->has('PasswordNew')) {
                $this->updatePass(request()->get('PasswordNew'), request()->get('Password'));
            }

            return response()->json(['Data' => $data, 'Cod' => 'ok', 'Message' => 'Datos actualizados correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['Data' => [], 'Cod' => 'error', 'Message' =>  $th->getMessage()], 400);
        }
    }

    /**
     * update pass
     *@param string  new pass
     *@return void
     */

    public function updatePass($newPass, $currentPass)
    {
        $agente = Agentes_Cliente::findOrfail(Auth::getUser()->Id_Agentes_Cliente);
        if (md5($currentPass)  ==   $agente->Password) {
            $agente->update([
                'Password' => md5($newPass)
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    }

}
