<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Subscription;

class UpdateOptionsValuesController extends Controller
{
    //
    public function updateOptionsValues(Request $request){
        
        $request->validate([
            'videoprofile' => 'required', 
            'idvideo' => 'required', 
            'payment_type' => 'required',
            'price' => 'required',
            'optiondelete' => 'required',
            'option' => 'required'
        ]);
        $videoprofile = $request->videoprofile;
        $idvideo = $request->idvideo;
        $payment_type = $request->payment_type;
        $price = $request->price;
        $optiondelete = $request->optiondelete;
        $option = $request->option;

        $contadorpareja = 0;
        $arrayData = array();

        $validateoptions = DB::select( DB::raw('select distinct optionvaluemix.idvideo from optionvaluemix where optionvaluemix.idvideo = "'.$idvideo.'";'));
        
        //return response()->json($videoprofile[0]['titlevideo']);

        if(count($validateoptions) != 0){
            
            $banderaelima = false;
            $eliminaoptionvaluemix = DB::table('optionvaluemix')
                ->where('optionvaluemix.idvideo',$idvideo)
                ->delete();
            
            if($eliminaoptionvaluemix){
                foreach ($optiondelete as $opt1) {
                    $eliminaoptionvalue = DB::table('optionvalue')
                        ->where('optionvalue.idoption',intval($opt1['optionkey_id']))
                        ->delete();
                    if($eliminaoptionvalue){
                        $eliminaoption = DB::table('options')
                            ->where('options.id',intval($opt1['optionkey_id']))
                            ->delete();
                    }
                }
                $banderaelima = true;
            }else{
                return response()->json([
                    'status'  => 401,
                    'message' => 'Acceso no autorizado',
                ], 401);
            }

            if($banderaelima == true){
                $updatevideo = DB::select( DB::raw('UPDATE videos SET titlevideo = "'.$videoprofile[0]['titlevideo'].'", VideoDescription = "'.$videoprofile[0]['VideoDescription'].'" ,urlimagen = "'.$videoprofile[0]['urlimagen'].'", public = "'.$videoprofile[0]['public'].'" ,idpmtype = "'.$payment_type.'", updated_at = now() WHERE videos.id = "'.$idvideo.'";') );


                foreach ($option as $opt) {
                    $results = DB::select( DB::raw('INSERT INTO options (id, idvideo, descripcion, created_at, updated_at) VALUES (NULL, "'.$idvideo.'", "'.$opt['optionkey'].'", now(), now());') ); 
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
                $contadorpareja2 = 0;
                $contadorpareja3 = 0;
                for($k = 0; $k<count($datareal); $k++){
                    $contadorpareja2 = $contadorpareja2 + 1;
                    $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "'.$price.'" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
                }
                for($k = 0; $k<count($datareal); $k++){
                    $contadorpareja3 = $contadorpareja3 + 1;
                    $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja3.'", "'.$idvideo.'", "'.$datareal[$k]->optionid3.'","'.$datareal[$k]->lastinsert3.'", "'.$price.'" , NULL, NULL,NULL,NULL, "'.$contadorpareja3.'" ,now(),now());') );
                }
                return response()->json(200);
            }
        }
        
    }

    public function updateOptionsValues0(Request $request){
        
        $request->validate([
            'videoprofile' => 'required', 
            'idvideo' => 'required', 
            'options_keys' => 'required',
            'options_keys_id' => 'required',
            'options_values' => 'required'
        ]);
        $videoprofile = $request->videoprofile;
        $idvideo = $request->idvideo;
        $options_keys = $request->options_keys;
        $options_keys_id = $request->options_keys_id;
        $options_values = $request->options_values;

        $contadorpareja = 0;
        $arrayData = array();

        //
        $arrayTransformado = array();
        $arraymodelo1_1 = array();
        $arraymodelo1_2 = array();

        $validateoptions = DB::select( DB::raw('select distinct optionvaluemix.idvideo from optionvaluemix where optionvaluemix.idvideo = "'.$idvideo.'";'));
        
        for($j = 0; $j<count($options_keys); $j++){   
            for($i = 0; $i<count($options_values); $i++){
                $optionsval = explode(" / ", $options_values[$i]['descripcion']);
                array_push($arrayTransformado, $optionsval[$j].'@#@'.$options_keys[$j].'@#@'.$options_values[$j]['precio']);
            }
        }
        $arrayTransformado = array_values(array_unique($arrayTransformado));
        //return response()->json($arrayTransformado);
        $ultimoresultado = array();
        for($i = 0; $i<count($arrayTransformado); $i++){
            $mymodels1[$i] = explode("@#@", $arrayTransformado[$i]);
            $optionss = '';
            $precioss = '';
            for($z = 0; $z<count($arrayTransformado); $z++){
                $mymodels2[$z] = explode("@#@", $arrayTransformado[$z]);
                if($mymodels1[$i][1] == $mymodels2[$z][1]){
                    if($optionss != ''){
                        $optionss = $mymodels2[$z][0].','.$optionss;
                        $precioss = $mymodels2[$z][2].','.$precioss;
                    }else{
                        $optionss = $mymodels2[$z][0];
                        $precioss = $mymodels2[$z][2];
                    }     
                }  
            }
            $model1 = (object) [
                'optionkey' => $mymodels1[$i][1],
                'optionval' => $optionss,
                'precio' => $precioss,
            ];
            array_push($ultimoresultado, $model1);
        }
        
        $ultimoresultado2 = array();
        $arraymodelo1_1 = array();
        $arraymodelo1_2 = array();
        $arraymodelo1_3 = array();
        for($i = 0; $i<count($ultimoresultado); $i++){
            array_push($arraymodelo1_1, $ultimoresultado[$i]->optionkey);
            array_push($arraymodelo1_2, $ultimoresultado[$i]->optionval);
            array_push($arraymodelo1_3, $ultimoresultado[$i]->precio);
        }
        $optionkey = array_values(array_unique($arraymodelo1_1));
        $optionval = array_values(array_unique($arraymodelo1_2));
        $precio = array_values(array_unique($arraymodelo1_3));
        for($i = 0; $i<count($optionkey); $i++){
            $model1_1 = (object) [
                'optionkey' => $optionkey[$i],
                'optionval' => $optionval[$i],
                'precio' => $precio[$i],
            ];
            array_push($ultimoresultado2, $model1_1);
        }
        //return response()->json($ultimoresultado2);
        $paratesteo = array();
        for($opt = 0; $opt<count($ultimoresultado2); $opt++){
            $optionsval = explode(",", $ultimoresultado2[$opt]->optionval);
            // para el precio
            $optionsprecio = explode(",", $ultimoresultado2[$opt]->precio);
            for($i = 0; $i<count($optionsval); $i++){
                $test = (object) [
                    'optionval' => $optionsval[$i],
                    'precio' => $optionsprecio[$i],
                    'optionkey' => count($optionsval),
                ];
                array_push($paratesteo, $test);
            } 
        }
        //return response()->json($paratesteo);
        $paratesteo2 = array();
        for($i = 0; $i<count($paratesteo); $i++){
            for($j = 0; $j<count($paratesteo); $j++){
                if($paratesteo[$i]->optionkey != $paratesteo[$j]->optionkey){
                    $test2 = (object) [
                        'optionval2' => $paratesteo[$i]->optionval,
                        'precio2' => $paratesteo[$i]->precio,
                        'optionval3' => $paratesteo[$j]->optionval,
                        'precio3' => $paratesteo[$j]->precio,
                    ];
                    array_push($paratesteo2, $test2);
                }
            }
        }

        //$operacionmitad = count($paratesteo2) / 2 ;
        //$datareal = array_slice($paratesteo2, 0,$operacionmitad);
        //return response()->json($datareal);
        //return response()->json($ultimoresultado2);

        if(count($validateoptions) != 0){
        //if(count($validateoptions) == 150){
            //return response()->json(true);
            $banderaelima = false;
            $eliminaoptionvaluemix = DB::table('optionvaluemix')
                ->where('optionvaluemix.idvideo',$idvideo)
                ->delete();
            
            if($eliminaoptionvaluemix){
                $eliminaoptionvalue = DB::table('optionvalue')
                ->join('options', 'options.id', '=', 'optionvalue.idoption')
                ->where('options.idvideo',$idvideo)
                ->delete();
                if($eliminaoptionvalue){
                    $eliminaoption = DB::table('options')
                        ->where('options.idvideo',$idvideo)
                        ->delete();
                    if($eliminaoption){
                        $banderaelima = true;
                        //return response()->json('data eliminada');
                    }    
                }
            }else{
                return response()->json([
                    'status'  => 401,
                    'message' => 'Acceso no autorizado',
                ], 401);
            }

            if($banderaelima == true){
                $updatevideo = DB::select( DB::raw('UPDATE videos SET titlevideo = "'.$videoprofile[0]['titlevideo'].'", VideoDescription = "'.$videoprofile[0]['VideoDescription'].'" ,urlimagen = "'.$videoprofile[0]['urlimagen'].'", public = "'.$videoprofile[0]['public'].'" ,idpmtype = "'.$videoprofile[0]['idpmtype'].'", updated_at = now() WHERE videos.id = "'.$idvideo.'";') );


                for($opt = 0; $opt<count($ultimoresultado2); $opt++){
                    $results = DB::select( DB::raw('INSERT INTO options (id, idvideo, descripcion, created_at, updated_at) VALUES (NULL, "'.$idvideo.'", "'.$ultimoresultado2[$opt]->optionkey.'", now(), now());') ); 
                    $id = DB::getPdo()->lastInsertId();
                    $contadorpareja = $contadorpareja + 1;
                    $optionsval = explode(",", $ultimoresultado2[$opt]->optionval);
                    // para el precio
                    $optionsprecio = explode(",", $ultimoresultado2[$opt]->precio);

                    for($i = 0; $i<count($optionsval); $i++){
                        $results2 = DB::select( DB::raw('INSERT INTO optionvalue (id, idoption, descripcion, id_subcription, created_at, updated_at) VALUES (NULL, "'.$id.'", "'.$optionsval[$i].'", NULL, now(),now());') );
                        $id2 = DB::getPdo()->lastInsertId();
                        $object = (object) [
                            'optionid' => $id,
                            'lastinsert' => $id2,
                            'optionval' => $optionsval[$i],
                            'precio' => $optionsprecio[$i],
                            'optionkey' => count($optionsval),
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
                                'precio2' => $arrayData[$i]->precio,
                                'optionid3' => $arrayData[$j]->optionid,
                                'lastinsert3' => $arrayData[$j]->lastinsert,
                                'optionval3' => $arrayData[$j]->optionval,
                                //'precio3' => $arrayData[$j]->precio,
                            ];
                            array_push($arrayData2, $object2);
                        }
                    }
                }
                $operacionmitad = count($arrayData2) / 2 ;
                $datareal = array_slice($arrayData2, 0,$operacionmitad);
                $contadorpareja2 = 0;
                //$contadorpareja3 = 0;
                
                for($k = 0; $k<count($datareal); $k++){
                    $contadorpareja2 = $contadorpareja2 + 1;
                    $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "'.$options_values[$k]['precio'].'" , "'.$options_values[$k]['img'].'", NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );

                    $results4 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid3.'","'.$datareal[$k]->lastinsert3.'", "'.$options_values[$k]['precio'].'" , "'.$options_values[$k]['img'].'", NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
                }
                return response()->json(200);
            }
        }else{
            return response()->json(false);
        }
        
    }
    
}
