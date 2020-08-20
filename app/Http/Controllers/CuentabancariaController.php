<?php

namespace App\Http\Controllers;

use App\Cuentabancaria;
use Illuminate\Http\Request;
use App\Http\Requests\CuentabancariaRequest;
use Illuminate\Support\Facades\DB;
class CuentabancariaController extends Controller{
    public function create(Request $request,$id_firebase){
        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if($userId){
            $objeto = Cuentabancaria::select('id','numero','banco')
                ->where('userId', $userId[0]->userId)
                ->get();
            if(sizeof($objeto)>0){
                return response()->json(false);
            }else{
                $objeto2 = Cuentabancaria::create([
                    'numero' =>  $request->numero,
                    'banco' =>  $request->banco,
                    'userId' =>  $userId[0]->userId
                    ]);
                if($objeto2){
                    return response()->json(true);
                }  
            }           
        } 
    }
    public function show($id_firebase){
        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if($userId){
            $objeto = Cuentabancaria::select('id','numero','banco')
                ->where('userId', $userId[0]->userId)
                ->get();
            if(sizeof($objeto)>0){
                return response()->json($objeto);
            }else{
                return response()->json(false);
            }          
        } 
    }
    public function update(Request $request, $id_firebase){
        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if($userId){
            $objeto = Cuentabancaria::select('id','numero','banco')
                ->where('userId', $userId[0]->userId)
                ->get();
            if(sizeof($objeto)>0){
                $objeto2 = DB::table('cuentabancarias')
                    ->where('id', $request->id)
                    ->update([
                    'numero' => $request->numero,
                    'banco' => $request->banco
                ]);
                if($objeto2){
                    return response()->json(true);
                } 
            }else{
                return response()->json(false);
            }            
        } 
    }
}
