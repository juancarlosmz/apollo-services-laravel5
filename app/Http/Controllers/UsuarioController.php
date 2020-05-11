<?php

namespace App\Http\Controllers;

use App\Usuario;
use App\Tipouser;
use App\Servicio;
use Illuminate\Http\Request;
use App\Http\Requests\UsuarioRequest;

class UsuarioController extends Controller{
    public function index(){
        $objeto = Usuario::all();
        $objeto->load('tipouser');
        $objeto->load('servicio');
        return $objeto ;
    }
    public function create(){
        //
    }
    public function store(UsuarioRequest $request){
        Usuario::create($request->all());
        return response()->json(true);
    }
    public function show(Usuario $usuario){
        //
    }
    public function edit(Usuario $usuario){
        //
    }
    public function update(Request $request, $id){
        $objeto = Usuario::findOrFail($id);
        $objeto->fill($request->all());
        $objeto->push();
        return response()->json(true);
    }
    public function destroy($id){
        $objeto = Usuario::findOrFail($id);
        $objeto->delete();
        return response()->json(true);
    }
}
