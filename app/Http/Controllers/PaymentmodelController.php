<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PaymentmodelController extends Controller{
    public function show(Request $request){
        $request->validate([
            'idioma' => 'required',
        ]);
        $idioma = $request->idioma;  
        $results = DB::select( DB::raw('select paymentmodeltype.id, traduccions.descripcion from paymentmodeltype INNER JOIN valuesidiomas ON valuesidiomas.id = paymentmodeltype.idvaluesidioma INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$idioma.'";'));
        if($results){
            return response()->json($results); 
        }else{
            $status = (object) [
                'status' => false
            ];
            return response()->json($status); 
        }
    }

}
