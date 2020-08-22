<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModeloProducto extends Controller{

    public function modelo1(Request $request){
        $request->validate([
            'videoid' => 'required', 
            'payment_type' => 'required', 
            'price' => 'required',  
            'option' => 'required'
        ]);
        $videoid = $request->videoid;
        $payment_type = $request->payment_type;
        $price = $request->price;
        $option = $request->option;

        $contadorpareja = 0;
        $arrayData = array();

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
        $operacionmitad = count($arrayData2) / 2 ;
        $datareal = array_slice($arrayData2, 0,$operacionmitad);
        //return response()->json($datareal);

        $contadorpareja2 = 0;
        $contadorpareja3 = 0;
        for($k = 0; $k<count($datareal); $k++){
            $contadorpareja2 = $contadorpareja2 + 1;
            $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$videoid.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "'.$price.'" , NULL, NULL, "'.$contadorpareja2.'" ,now(),now());') );
        }
        for($k = 0; $k<count($datareal); $k++){
            $contadorpareja3 = $contadorpareja3 + 1;
            $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja3.'", "'.$videoid.'", "'.$datareal[$k]->optionid3.'","'.$datareal[$k]->lastinsert3.'", "'.$price.'" , NULL, NULL, "'.$contadorpareja3.'" ,now(),now());') );
        }

        return response()->json(200);
    
    }


    public function modelo2(Request $request){
        $request->validate([
            'videoid' => 'required', 
            'option' => 'required',
            'optionvalues' => 'required',   
        ]);
        $videoid = $request->videoid;
        $option = $request->option;
        $optionvalues = $request->optionvalues;

        $contadorpareja = 0;

        $results = DB::select( DB::raw('INSERT INTO options (id, idvideo, descripcion, created_at, updated_at) VALUES (NULL, "'.$videoid.'", "'.$option.'", now(), now());') ); 

        $id = DB::getPdo()->lastInsertId(); 
        foreach ($optionvalues as $options) {

            $results2 = DB::select( DB::raw('INSERT INTO optionvalue (id, idoption, descripcion, id_subcription, created_at, updated_at) VALUES (NULL, "'.$id.'", "'.$options['option_description'].'", NULL, now(),now());') );

            $contadorpareja = $contadorpareja + 1;
            $id2 = DB::getPdo()->lastInsertId();

            $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja.'", "'.$videoid.'", "'.$id.'","'.$id2.'", "'.$options['price'].'" , NULL, "'.$options['id_item_stripe'].'", "'.$options['sort'].'" ,now(),now());') );

            

        }

        return response()->json(200);

    }

    public function modelo3(Request $request){
        $request->validate([
            'videoid' => 'required', 
            'payment_type' => 'required', 
            'price' => 'required',  
            'option' => 'required'
        ]);
        $videoid = $request->videoid;
        $payment_type = $request->payment_type;
        $price = $request->price;
        $option = $request->option;

        $contadorpareja = 0;

        foreach ($option as $opt) {

            $results = DB::select( DB::raw('INSERT INTO options (id, idvideo, descripcion, created_at, updated_at) VALUES (NULL, "'.$videoid.'", "'.$opt['optionkey'].'", now(), now());') ); 

            $id = DB::getPdo()->lastInsertId();
            
            $contadorpareja = $contadorpareja + 1;

            foreach ($opt['optionval'] as $optval) {

                $results2 = DB::select( DB::raw('INSERT INTO optionvalue (id, idoption, descripcion, id_subcription, created_at, updated_at) VALUES (NULL, "'.$id.'", "'.$optval['optionvalkey'].'", NULL, now(),now());') );

                
                $id2 = DB::getPdo()->lastInsertId();

                $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja.'", "'.$videoid.'", "'.$id.'","'.$id2.'", "'.$price.'" , NULL, NULL, "'.$contadorpareja.'" ,now(),now());') );

            }

            
        }

        return response()->json(200);

    }

}
