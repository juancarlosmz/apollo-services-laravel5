<?php

namespace App\Http\Controllers;

use App\Tipouser;
use Illuminate\Http\Request;
use App\Http\Requests\TipouserRequest;

class TipouserController extends Controller{
    public function index(){
        //
        $objeto = Tipouser::all();
        return $objeto;
    }
    public function create(){
        //
    }
    public function store(TipouserRequest $request){
        //
        Tipouser::create($request->all());
        return response()->json(true);
    }
    public function show(Tipouser $tipouser){
        //
    }
    public function edit(Tipouser $tipouser){
        //
    }
    public function update(TipouserRequest $request, $id){
        //
        $objeto = Tipouser::findOrFail($id);
        $objeto->update($request->all());
        return response()->json(true);
    }
    public function destroy($id){
        //
        $objeto = Tipouser::findOrFail($id);
        $objeto->delete();
        return response()->json(true);
    }
}
