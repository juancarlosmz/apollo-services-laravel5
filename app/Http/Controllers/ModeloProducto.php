<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Subscription;

class ModeloProducto extends Controller{

    public function modelo1(Request $request){
        $request->validate([
            'videoid' => 'required', 
            'payment_type' => 'required',   
            'option' => 'required'
        ]);
        $videoid = $request->videoid;
        $payment_type = $request->payment_type;
        $option = $request->option;

        $contadorpareja = 0;
        $arrayData = array();

        $validateoptions = DB::select( DB::raw('select distinct optionvaluemix.idvideo from optionvaluemix where optionvaluemix.idvideo = "'.$videoid.'";'));

        //return response()->json(count($validateoptions));
        $contadoropcions = count($option);
        if(count($validateoptions) == 0){
            $updateidmtype = DB::select( DB::raw('UPDATE videos SET idpmtype = "'.$payment_type.'", updated_at = now() WHERE videos.id = "'.$videoid.'";') );

            foreach ($option as $opt) {
                $results = DB::select( DB::raw('INSERT INTO options (id, idvideo, descripcion, created_at, updated_at) VALUES (NULL, "'.$videoid.'", "'.$opt['optionkey'].'", now(), now());') ); 
                $id = DB::getPdo()->lastInsertId();
                $contadorpareja = $contadorpareja + 1;
                $optionsval = explode(",", $opt['optionval']);
                for($i = 0; $i<count($optionsval); $i++){
                    $results2 = DB::select( DB::raw('INSERT INTO optionvalue (id, idoption, descripcion, id_subcription, created_at, updated_at) VALUES (NULL, "'.$id.'", "'.$optionsval[$i].'", NULL, now(),now());') );
                    $id2 = DB::getPdo()->lastInsertId();
                    $object = (object) [
                        'optionid' => $id,
                        'lastinsert' => $id2,
                        'optionval' => $optionsval[$i],
                        'optionkey' => count($option),
                    ];
                    array_push($arrayData, $object);
                }
            }
            $arrayData2 = array();
            // segun la cantidad de options
            if($contadoropcions == 1){
                for($i = 0; $i<count($arrayData); $i++){  
                    $object2 = (object) [
                        'optionid2' => $arrayData[$i]->optionid,
                        'lastinsert2' => $arrayData[$i]->lastinsert,
                    ];
                    array_push($arrayData2, $object2);
                }
                $datareal = $arrayData2;
                $contadorpareja2 = 0;
                //return response()->json($datareal);
                for($k = 0; $k<count($datareal); $k++){
                    $contadorpareja2 = $contadorpareja2 + 1;
                    $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$videoid.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
                }
                //return response()->json($arrayData2);
            }else if($contadoropcions == 2){
                for($i = 0; $i<count($arrayData); $i++){  
                    for($j = 0; $j<count($arrayData); $j++){
                        if($arrayData[$i]->optionid != $arrayData[$j]->optionid){
                            $object2 = (object) [
                                'optionid2' => $arrayData[$i]->optionid,
                                'lastinsert2' => $arrayData[$i]->lastinsert,
                                'optionval2' => $arrayData[$i]->optionval,
                                'optionid3' => $arrayData[$j]->optionid,
                                'lastinsert3' => $arrayData[$j]->lastinsert,
                                'optionval3' => $arrayData[$j]->optionval,
                            ];
                            array_push($arrayData2, $object2);
                        }
                    }
                }
                $operacionmitad = count($arrayData2) / $contadoropcions ;
                $datareal = array_slice($arrayData2, 0,$operacionmitad);
                $contadorpareja2 = 0;
                //return response()->json($datareal);
                for($k = 0; $k<count($datareal); $k++){
                    $contadorpareja2 = $contadorpareja2 + 1;
                    $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$videoid.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
    
                    $results4 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$videoid.'", "'.$datareal[$k]->optionid3.'","'.$datareal[$k]->lastinsert3.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
                }
            }else if($contadoropcions == 3){
                //return response()->json($arrayData);
                
                for($i = 0; $i<count($arrayData); $i++){  
                    $valork = 0;
                    for($j = 0; $j<count($arrayData); $j++){

                        if($arrayData[$j]->optionid != $valork){
                            for($k = 0; $k<count($arrayData); $k++){
                                $valork = $arrayData[$k]->optionid;
                                if($arrayData[$i]->optionid != $arrayData[$j]->optionid){
                                    if($arrayData[$i]->optionid != $arrayData[$k]->optionid){
                                        if($arrayData[$j]->optionid != $arrayData[$k]->optionid){
                                            $object2 = (object) [
                                                'optionid2' => $arrayData[$i]->optionid,
                                                'lastinsert2' => $arrayData[$i]->lastinsert,
                                                'optionval2' => $arrayData[$i]->optionval,
                                                'optionid3' => $arrayData[$j]->optionid,
                                                'lastinsert3' => $arrayData[$j]->lastinsert,
                                                'optionval3' => $arrayData[$j]->optionval,
                                                'optionid4' => $arrayData[$k]->optionid,
                                                'lastinsert4' => $arrayData[$k]->lastinsert,
                                                'optionval4' => $arrayData[$k]->optionval,
                                            ];
                                            array_push($arrayData2, $object2);

                                        }
                                    }
                                }
                            }
                        }
                        
                    }
                }
                
                $operacionmitad = count($arrayData2) / 4;
                $datareal = array_slice($arrayData2, 0,$operacionmitad);
                $contadorpareja2 = 0;
                //return response()->json($datareal);

                for($k = 0; $k<count($datareal); $k++){
                    $contadorpareja2 = $contadorpareja2 + 1;
                    $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$videoid.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
    
                    $results4 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$videoid.'", "'.$datareal[$k]->optionid3.'","'.$datareal[$k]->lastinsert3.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );

                    $results5 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$videoid.'", "'.$datareal[$k]->optionid4.'","'.$datareal[$k]->lastinsert4.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
                }
            }
            return response()->json(200);
        }else{
            return response()->json("ACTUALIZA LOS CAMPOS");
        }  
    }

    public function modelo2(Request $request){
        $request->validate([
            'videoid' => 'required', 
            'payment_type' => 'required', 
            'option' => 'required',
            'optionvalues' => 'required',   
        ]);

        // stripe    
        try {
            if (!$request->has("api_key")) {
                return response()->json([
                    'status'  => 401,
                    'message' => 'Acceso no autorizado',
                ], 401);
            }
            if ($request->has("api_key")) {
                $api_key = "6WugwSv7Ns3fbi51fHir48ckcpG1rKxW";
                if ($request->api_key != $api_key) {
                    return response()->json([
                    'status' => 401,
                    'message' => 'Acceso no autorizado',
                    ], 401);
                }
            }
            Stripe::setApiKey('sk_test_3RP8Mjx7h8bC6IeVcqOaSDFA');
            $product = Product::create(array(
                'name' => $request->option,
                'type' => 'service',
            ));
            $data = [];
            $opciones = $request->optionvalues;
            $items = [];
            foreach ($opciones as $option) {
                $price = Price::create(array(
                    'unit_amount' => $option['price'],
                    'currency'    => 'usd',
                    'recurring'   => [
                        'interval'       => $option['interval'],
                        'interval_count' => $option['interval_count'],
                    ],
                    'product'     => $product->id
                ));
                $items[] = [
                    "id" => $price->id
                ];
                usleep(500000);
            }
            $data = [
                'product' => $product->id,
                'tarifas' => $items,
            ];
            $tarifas = $data['tarifas'];

            // mandando a la base de datos
            $videoid = $request->videoid;
            $payment_type = $request->payment_type;
            $option = $request->option;
            $optionvalues = $request->optionvalues;
            $contadorpareja = 0;

            $updateidmtype = DB::select( DB::raw('UPDATE videos SET idpmtype = "'.$payment_type.'", updated_at = now() WHERE videos.id = "'.$videoid.'";') );

            $results = DB::select( DB::raw('INSERT INTO options (id, idvideo, descripcion, created_at, updated_at) VALUES (NULL, "'.$videoid.'", "'.$option.'", now(), now());') ); 
            $id = DB::getPdo()->lastInsertId(); 
            for ($options = 0;$options<count($optionvalues); $options++) {
                $results2 = DB::select( DB::raw('INSERT INTO optionvalue (id, idoption, descripcion, id_subcription, created_at, updated_at) VALUES (NULL, "'.$id.'", "'.$optionvalues[$options]['option_description'].'", NULL, now(),now());') );

                $contadorpareja = $contadorpareja + 1;
                $id2 = DB::getPdo()->lastInsertId();
                for($i = 0; $i<count($tarifas); $i++){
                    if($options == $i){
                        $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja.'", "'.$videoid.'", "'.$id.'","'.$id2.'", "'.$optionvalues[$options]['price'].'" , NULL, "'.$tarifas[$options]['id'].'", "'.$optionvalues[$options]['interval'].'","'.$optionvalues[$options]['interval_count'].'", "'.$optionvalues[$options]['sort'].'" ,now(),now());') );
                    }
                    
                }
            }
            return response()->json(200);
            // end envio a la base de datos
        } catch (\Exception $ex) {
            $msg['response'] = [
                "error" => $ex->getMessage()
            ];
            return \Response::json($msg, 200);
        }   
        // end stripe
    }
    public function modelo3(Request $request){
        $request->validate([
            'videoid' => 'required', 
            'payment_type' => 'required', 
            'option' => 'required',
            'optionvalues' => 'required',   
        ]);
        $videoid = $request->videoid;
        $payment_type = $request->payment_type;
        $option = $request->option;
        $optionvalues = $request->optionvalues;
        $contadorpareja = 0;
        
        $updateidmtype = DB::select( DB::raw('UPDATE videos SET idpmtype = "'.$payment_type.'", updated_at = now() WHERE videos.id = "'.$videoid.'";') );

        $results = DB::select( DB::raw('INSERT INTO options (id, idvideo, descripcion, created_at, updated_at) VALUES (NULL, "'.$videoid.'", "'.$option.'", now(), now());') ); 
        $id = DB::getPdo()->lastInsertId(); 
        foreach ($optionvalues as $options) {
            $results2 = DB::select( DB::raw('INSERT INTO optionvalue (id, idoption, descripcion, id_subcription, created_at, updated_at) VALUES (NULL, "'.$id.'", "'.$options['option_description'].'", NULL, now(),now());') );

            $contadorpareja = $contadorpareja + 1;
            $id2 = DB::getPdo()->lastInsertId();

            $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja.'", "'.$videoid.'", "'.$id.'","'.$id2.'", "'.$options['price'].'" , NULL, NULL,NULL,NULL, "'.$options['sort'].'" ,now(),now());') );
        }
        return response()->json(200);
    }

}
