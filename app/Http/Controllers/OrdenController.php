<?php
namespace App\Http\Controllers;
use App\Orden;
use App\Ordencambiaestado;
use App\Transaccion;
use App\Paymentmodel;
use Illuminate\Http\Request;
use App\Http\Requests\OrdenRequest;
use Illuminate\Support\Facades\DB;
class OrdenController extends Controller{
    public function create(Request $request){
        $maxnumbervalue;
        $userIdvendedor = DB::table('usuarios')
                ->select('usuarios.id as userIdvendedor')
                ->where('usuarios.id_firebase', $request->userIdvendedor)
                ->get();
        $userIdcomprador = DB::table('usuarios')
                ->select('usuarios.id as userIdcomprador')
                ->where('usuarios.id_firebase', $request->userIdcomprador)
                ->get(); 
        //$maxid = Orden::max('id');
        $maxid = DB::select( DB::raw('select MAX(transaccions.ordenId) AS id FROM transaccions INNER JOIN  usuarios ON usuarios.id = transaccions.userIdvendedor INNER JOIN ordens ON ordens.id = transaccions.ordenId WHERE transaccions.userIdvendedor = "'.$userIdvendedor[0]->userIdvendedor.'"') );   

        if($maxid[0]->id){
            $maxnumber = Orden::select('numero')->where('id', $maxid[0]->id)->get();
            $maxnumbervalue = $maxnumber[0]->numero;
        }else{
            $maxnumbervalue = 120;
        }
        $objeto = Orden::create([
            'numero' => $maxnumbervalue,
            'descripcion' => $request->descripcion,
            'total' => $request->total,
            'fechaentrega' => $request->fechaentrega
            ]);  
        if($objeto){

            $id = DB::getPdo()->lastInsertId();  

            $inserttracking = DB::select( DB::raw('INSERT INTO tracking (id, idusuario, idvideo, idnegocio, idorden, idpago ,created_at, updated_at) VALUES (NULL, "'.$userIdcomprador[0]->userIdcomprador.'",0,0,"'.$id.'",0,now(), now());') );
            
            $numeroincrement = Orden::find($id)->increment('numero');
            // Agrega una orden con el estado == 1 => creada
            $objeto2 = Ordencambiaestado::create([
                'ordenId' => $id,
                'ordenestadoId' => 1
                ]);
            // creando el tipo de pago c/s membresia    
            $Paymentmodel = Paymentmodel::create([
                'idorden' => $id,
                'idpmtype' => $request->idpmtype,
                ]);  
            $resultsPaymentmodel = DB::select( DB::raw('select paymentmodeltype.id, traduccions.descripcion from paymentmodeltype INNER JOIN valuesidiomas ON valuesidiomas.id = paymentmodeltype.idvaluesidioma INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$request->idioma.'" and paymentmodeltype.id = "'.$Paymentmodel->idpmtype.'";'));  
            if($objeto2){
                // Agrega una transaccion entre el cliente y vendedor
                $objeto3 = Transaccion::create([
                    'userIdvendedor' => $userIdvendedor[0]->userIdvendedor,
                    'userIdcomprador' => $userIdcomprador[0]->userIdcomprador,
                    'ordenId' => $id
                    ]);
                if($objeto3){
                    $mydata = (object) [
                        'ordenid' => $objeto->id,
                        'ordentitle' => $objeto->numero + 1,
                        'descripcion' => $objeto->descripcion,
                        'total' => $objeto->total,
                        'fechaentrega' => $objeto->fechaentrega,
                        'idpmtype' => $resultsPaymentmodel[0]->id,
                        'paymentdescripcion' => $resultsPaymentmodel[0]->descripcion,
                    ];
                    return response()->json($mydata);
                }
            }
        }
    }
}
