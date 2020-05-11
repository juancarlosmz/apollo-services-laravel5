<?php

namespace App\Http\Controllers;

use App\Pago;
use Illuminate\Http\Request;
use App\Http\Requests\PagoRequest;

class PagoController extends Controller{
    public function index(){
        //
        $objeto = Pago::all();
        return $objeto ;
    }
    public function create(){
        //
    }
    public function store(PagoRequest $request){
        //
        Pago::create($request->all());
        return response()->json(true);
    }
    public function show(Pago $pago){
        //
    }
    public function edit(Pago $pago){
        //
    }
    public function update(Request $request, $id){
        //
        $objeto = Pago::findOrFail($id);
        $objeto->fill($request->all());
        $objeto->push();
        return response()->json(true);
    }
    public function destroy($id){
        //
        $objeto = Pago::findOrFail($id);
        $objeto->delete();
        return response()->json(true);
    }
}
