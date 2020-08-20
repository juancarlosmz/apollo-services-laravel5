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

        $objeto = Pago::create([
            'value' => $request->secret,
            'fecha' => $request->fecha,
            'ordenId' => $request->ordenId
        ]);

        if($objeto){
            $objeto2 = Ordencambiaestado::create([
                'ordenId' => $request->ordenId,
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
