<?php
namespace App\Http\Controllers;
use App\Pago;
use App\Ordencambiaestado;
use Illuminate\Http\Request;
use App\Http\Requests\PagoRequest;
use Illuminate\Support\Facades\DB;
//stripe
\Stripe\Stripe::setApiKey('sk_test_3RP8Mjx7h8bC6IeVcqOaSDFA');
class PagoController extends Controller{
    public function create(Request $request){

        $request->validate([
            'secret' => 'required',
            'fecha' => 'required',
            'ordenId' => 'required',
            'cliente_isocurrency' => 'required',
            'cliente_tc' => 'required',
            'business_isocurrency' => 'required',
            'business_tc' => 'required',
        ]);

        $secret = $request->secret;
        $fecha = $request->fecha;
        $ordenId = $request->ordenId;
        $cliente_isocurrency = $request->cliente_isocurrency;
        $cliente_tc = $request->cliente_tc;
        $business_isocurrency = $request->business_isocurrency;
        $business_tc = $request->business_tc;

        // esta solo es data de ejemplo - eliminar y descomentar
/*
        $cliente_isocurrency = 'CAD';
        $cliente_tc = 1.318703;
        $business_isocurrency = 'PEN';
        $business_tc = 0.36861030569121;
        */

        $objeto = Pago::create([
            'value' => $secret,
            'fecha' => $fecha,
            'ordenId' => $ordenId,
            'cliente_isocurrency' => $cliente_isocurrency,
            'cliente_tc' => $cliente_tc,
            'business_isocurrency' => $business_isocurrency,
            'business_tc' => $business_tc,
        ]);

        if($objeto){
            $id = DB::getPdo()->lastInsertId();  

            $userIdcomprador = DB::table('transaccions')
                ->select('transaccions.userIdcomprador')
                ->where('transaccions.ordenId', $ordenId)
                ->get();

            $inserttracking = DB::select( DB::raw('INSERT INTO tracking (id, idusuario, idvideo, idnegocio, idorden, idpago ,created_at, updated_at) VALUES (NULL, "'.$userIdcomprador[0]->userIdcomprador.'",0,0,0,"'.$ordenId.'",now(), now());') );

            $objeto2 = Ordencambiaestado::create([
                'ordenId' => $ordenId,
                'ordenestadoId' => 2
            ]);
            if($objeto2){
                return response(200);
            }
        }
    }
    public function create2(Request $request){
        $valuedecimal = (number_format($request->amount, 2, '.', '') * 100);
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $valuedecimal,
            'currency' => $request->currency,
        ]);
        $client_secret = $intent->client_secret;
        $object = (object) [
            'key' => $client_secret,
        ]; 
        return response()->json($object);
    }
    public function create3(Request $request){
        $data = DB::table('pagos')
            ->select('pagos.ordenId')
            ->where('pagos.ordenId', $request->orderId)
            ->get();  
        if(count($data) == 0){
            $status = (object) [
                'status' => true
            ];
            return response()->json($status);
        }else{
            $status = (object) [
                'status' => false
            ];
            return response()->json($status); 
        }
    }

    
}
