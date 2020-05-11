<?php

namespace App\Http\Controllers;

use App\Cuentabancaria;
use Illuminate\Http\Request;
use App\Http\Requests\CuentabancariaRequest;

class CuentabancariaController extends Controller{
    public function index(){
        //
        $objeto = Cuentabancaria::all();
        $objeto->load('usuario');
        return $objeto ;
    }
    public function create(){
        //
    }
    public function store(CuentabancariaRequest $request){
        //
        Cuentabancaria::create($request->all());
        return response()->json(true);
    }
    public function show(Cuentabancaria $cuentabancaria){
        //
    }
    public function edit(Cuentabancaria $cuentabancaria){
        //
    }
    public function update(Request $request, $id){
        //
        $objeto = Cuentabancaria::findOrFail($id);
        $objeto->fill($request->all());
        $objeto->push();
        return response()->json(true);
    }
    public function destroy($id){
        //
        $objeto = Cuentabancaria::findOrFail($id);
        $objeto->delete();
        return response()->json(true);
    }
}
