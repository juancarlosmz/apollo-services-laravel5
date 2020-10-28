<?php
namespace App\Http\Controllers;
use App\Ordencambiaestado;
use Illuminate\Http\Request;
use App\Http\Requests\OrdencambiaestadoRequest;
use Illuminate\Support\Facades\DB;
class OrdencambiaestadoController extends Controller{
    public function create(Request $request, $idioma){
        $request->validate([
            'ordenId' => 'required',
        ]);

        $ordenId = $request->ordenId;

        $ordervalidado = DB::table('ordencambiaestados')
            ->select('id')
            ->where('ordencambiaestados.ordenId', $ordenId)
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
                'ordenId' =>  $ordenId,
                'ordenestadoId' =>  3,
                ]);
            if($objeto){

                $userIdcomprador = DB::table('transaccions')
                    ->select('transaccions.userIdcomprador')
                    ->where('transaccions.ordenId', $ordenId)
                    ->get();    

                $inserttracking = DB::select( DB::raw('INSERT INTO tracking (id, idusuario, idvideo, idnegocio, idorden, idpago ,identregado,idrecibido,created_at, updated_at) VALUES (NULL, "'.$userIdcomprador[0]->userIdcomprador.'",0,0,0,0,"'.$ordenId.'",0,now(), now());') );

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
        $request->validate([
            'ordenId' => 'required',
        ]);
        $ordenId = $request->ordenId;
        $ordervalidado = DB::table('ordencambiaestados')
            ->select('id')
            ->where('ordencambiaestados.ordenId', $ordenId)
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
                'ordenId' =>  $ordenId,
                'ordenestadoId' =>  4,
                ]);
            if($objeto){

                $userIdcomprador = DB::table('transaccions')
                    ->select('transaccions.userIdcomprador')
                    ->where('transaccions.ordenId', $ordenId)
                    ->get();   

                $inserttracking = DB::select( DB::raw('INSERT INTO tracking (id, idusuario, idvideo, idnegocio, idorden, idpago ,identregado,idrecibido,created_at, updated_at) VALUES (NULL, "'.$userIdcomprador[0]->userIdcomprador.'",0,0,0,0,0,"'.$ordenId.'",now(), now());') );
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
