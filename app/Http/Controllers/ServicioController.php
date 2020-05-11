<?php

namespace App\Http\Controllers;

use App\Servicio;
use Illuminate\Http\Request;
use App\Http\Requests\ServicioRequest;

class ServicioController extends Controller{
    public function index(){
        //
        $objeto = Servicio::all();
        return $objeto;
    }
    public function create(){
        //
    }
    public function store(ServicioRequest $request){
        Servicio::create($request->all());
        return response()->json(true);
    }
    public function show(Servicio $servicio){
        //  
    }
    public function edit($id){
        //
    }
    public function update(ServicioRequest $request, $id){
        //
        $objeto = Servicio::findOrFail($id);
        $objeto->update($request->all());
        return response()->json(true);

    }
    public function destroy($id){
        //
        $objeto = Servicio::findOrFail($id);
        $objeto->delete();
        return response()->json(true);
    }
}
