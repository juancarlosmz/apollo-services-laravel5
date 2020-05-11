<?php

namespace App\Http\Controllers;

use App\Deposito;
use Illuminate\Http\Request;
use App\Http\Requests\DepositoRequest;

class DepositoController extends Controller{
    public function index(){
        //
        $objeto = Deposito::all();
        $objeto->load('usuario');
        return $objeto ;
    }
    public function create(){
        //
    }
    public function store(DepositoRequest $request){
        //
        Deposito::create($request->all());
        return response()->json(true);
    }
    public function show(Deposito $deposito){
        //
    }
    public function edit(Deposito $deposito){
        //
    }
    public function update(Request $request, $id){
        //
        $objeto = Deposito::findOrFail($id);
        $objeto->fill($request->all());
        $objeto->push();
        return response()->json(true);
    }
    public function destroy($id){
        //
        $objeto = Deposito::findOrFail($id);
        $objeto->delete();
        return response()->json(true);
    }
}
