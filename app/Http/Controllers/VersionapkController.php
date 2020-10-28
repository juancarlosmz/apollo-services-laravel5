<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VersionapkController extends Controller{
    public function versionapk($versionapk){
        
        $results = DB::select( DB::raw('SELECT descripcion FROM versionapk WHERE id = (SELECT max(id) FROM versionapk)'));

        if($results){
            if($results[0]->descripcion == $versionapk){
                return response()->json([
                    'status' => true,
                    'message' => 'Las versiones son iguales',
                  ], 200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Hay una nueva actualizacion',
                  ], 200);
            }
        }else{
            return response()->json([
                400 => 'Bad Request',
              ], 400);
        }
    }
}
