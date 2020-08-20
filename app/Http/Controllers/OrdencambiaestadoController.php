<?php
namespace App\Http\Controllers;
use App\Ordencambiaestado;
use Illuminate\Http\Request;
use App\Http\Requests\OrdencambiaestadoRequest;
use Illuminate\Support\Facades\DB;
class OrdencambiaestadoController extends Controller{
    public function create(Request $request, $idioma){
        $ordervalidado = DB::table('ordencambiaestados')
            ->select('id')
            ->where('ordencambiaestados.ordenId', $request->ordenId)
            ->where('ordencambiaestados.ordenestadoId', 3)
            ->get();
        $recibido = DB::table('ordenestados')
            ->join('valuesidiomas', 'ordenestados.validiomaId', '=', 'valuesidiomas.id')
            ->join('traduccions', 'traduccions.validiomaId', '=', 'valuesidiomas.id')
            ->join('idiomas', 'traduccions.idiomaId', '=', 'idiomas.id')
            ->select('ordenestados.descripcion as estado','traduccions.descripcion as traduccion')
            ->where('idiomas.id', $idioma)
            ->where('ordenestados.id', 3)
            ->get();    
        if(count($ordervalidado) == 0){
            $objeto = Ordencambiaestado::create([
                'ordenId' =>  $request->ordenId,
                'ordenestadoId' =>  3,
                ]);
            if($objeto){
                return response()->json($recibido);
            }
        }else{
            $status = [(object) [
                'estado' => '','traduccion' => ''
            ]];
            return response()->json($status); 
        }  
    }
    public function create2(Request $request, $idioma){
        $ordervalidado = DB::table('ordencambiaestados')
            ->select('id')
            ->where('ordencambiaestados.ordenId', $request->ordenId)
            ->where('ordencambiaestados.ordenestadoId', 4)
            ->get();
        $entregado = DB::table('ordenestados')
            ->join('valuesidiomas', 'ordenestados.validiomaId', '=', 'valuesidiomas.id')
            ->join('traduccions', 'traduccions.validiomaId', '=', 'valuesidiomas.id')
            ->join('idiomas', 'traduccions.idiomaId', '=', 'idiomas.id')
            ->select('ordenestados.descripcion as estado','traduccions.descripcion as traduccion')
            ->where('idiomas.id', $idioma)
            ->where('ordenestados.id', 4)
            ->get();    
        if(count($ordervalidado) == 0){
            $objeto = Ordencambiaestado::create([
                'ordenId' =>  $request->ordenId,
                'ordenestadoId' =>  4,
                ]);
            if($objeto){
                return response()->json($entregado);
            }
        }else{
            $status = [(object) [
                'estado' => '','traduccion' => ''
            ]];
            return response()->json($status); 
        }     
    }
}
