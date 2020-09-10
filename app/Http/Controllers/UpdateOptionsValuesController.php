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
    public function updatevideopublic(Request $request){
        $request->validate([
            'idvideo' => 'required', 
            'public' => 'required',
        ]);
        $idvideo = $request->idvideo;
        $public = $request->public;

        $updatevideo = DB::select( DB::raw('UPDATE videos SET public = "'.$public.'", updated_at = now() WHERE videos.id = "'.$idvideo.'";') );
 
        return response()->json(200);
    }
    //
    public function addoptionvaluemix(Request $request){
        $request->validate([
            'idvideo' => 'required', 
            'payment_type' => 'required',
            'option' => 'required'
        ]);
        $idvideo = $request->idvideo;
        $payment_type = $request->payment_type;
        $option = $request->option;
        $price = 0;
        $contadorpareja = 0;
        $arrayData = array();
        $validateoptions = DB::select( DB::raw('select distinct optionvaluemix.idvideo from optionvaluemix where optionvaluemix.idvideo = "'.$idvideo.'";'));
        $contadordeopciones = count($option);
        //return response()->json(count($option));
        if(count($validateoptions) != 0){
            $banderaelima = false;
            $esinsertoupdate = false;
            if($banderaelima == false){
                $index = -1;
                foreach ($option as $opt) {
                    $index++;
                    $optionkeyid = $opt['optionkeyid'];
                    $optionvalid = explode(",", $opt['optionvalid']);
                    $optionsval = explode(",", $opt['optionval']);
                    $optionsvalcompare = explode(",", $opt['optionval']);
                    for($i = 0; $i<count($optionvalid); $i++){
                        if($optionvalid[$i] == 0){

                            //return response()->json(key($optionvalid));
                            /*
                            unset($optionsvalcompare[$i]);
                            for($j = 0; $j<count($optionsvalcompare); $j++){
                                if(strtoupper($optionsval[$i]) == strtoupper($optionsvalcompare[$j])){
                                    return response()->json([
                                        'status' => 401,
                                        'message' => $optionsval[$i].' se repite',
                                      ], 401);
                                }
                            }*/
                            $results2 = DB::select( DB::raw('INSERT INTO optionvalue (id, idoption, descripcion, id_subcription, created_at, updated_at) VALUES (NULL, "'.$optionkeyid.'", "'.$optionsval[$i].'", NULL, now(),now());') );
                            $id2 = DB::getPdo()->lastInsertId();
                            $object = (object) [
                                'optionid' => $optionkeyid,
                                'lastinsert' => $id2,
                                'optionval' => $optionsval[$i],
                                'optionkey' => count($option),
                            ];
                            array_push($arrayData, $object);

                        }
                    }
                    $esinsertoupdate = true;
                }
                //return response()->json($arrayData);
                if($esinsertoupdate == true){
                    // ingreso de las opciones con 1 2 3 
                    if($contadordeopciones == 1){
                        $ultimovalorcontador = DB::select( DB::raw('SELECT MAX(id) as id FROM optionvaluemix WHERE idvideo = "'.$idvideo.'";') );
                        $contadorpareja2 = $ultimovalorcontador[0]->id;
                        for($k = 0; $k<count($arrayData); $k++){
                            $contadorpareja2 = $contadorpareja2 + 1;
                            $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$arrayData[$k]->optionid.'","'.$arrayData[$k]->lastinsert.'", "'.$price.'" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
        
                        }
                        return response()->json(200);
                    }else if($contadordeopciones == 2){
                        $optionsvalid2 = '';
                        $arrayData2 = array();
                        $arrayParacomparar = array();
                        for($i = 0; $i<count($arrayData); $i++){ 
                            for($j = 0; $j<count($option); $j++){ 
                                if($arrayData[$i]->optionid != $option[$j]['optionkeyid']){
                                    $optionsval2 = explode(",", $option[$j]['optionval']);
                                    $optionsvalid2 = explode(",", $option[$j]['optionvalid']);
                                    //agregar optionvaluemix
                                    for($k = 0; $k<count($optionsvalid2); $k++){
                                        // reemplazar valores
                                        $myreplace = $optionsvalid2[$k];
                                        if($optionsvalid2[$k] == 0){
                                            for($x = 0; $x<count($arrayData); $x++){
                                                if($optionsval2[$k] == $arrayData[$x]->optionval){
                                                    $myreplace = $arrayData[$x]->lastinsert;
                                                }
                                            }
                                        }
                                        // ingresa a la condicion
                                        if($j == 0){
                                            array_push($arrayParacomparar, intval($option[$j]['optionkeyid']).'@#@'.intval($myreplace).'@#@'.$optionsval2[$k].'@#@'.intval($arrayData[$i]->optionid).'@#@'.intval($arrayData[$i]->lastinsert).'@#@'.$arrayData[$i]->optionval);
                                        }else{
                                            array_push($arrayParacomparar, intval($arrayData[$i]->optionid).'@#@'.intval($arrayData[$i]->lastinsert).'@#@'.$arrayData[$i]->optionval.'@#@'.intval($option[$j]['optionkeyid']).'@#@'.intval($myreplace).'@#@'.$optionsval2[$k]);
                                        }
                                    }
                                }
                            }
                        }
                        $arrayaconvertir = array_values(array_unique($arrayParacomparar));
                        $arrayData3 = array();
                        for($x = 0; $x<count($arrayaconvertir); $x++){
                            $changuevaluess = explode("@#@", $arrayaconvertir[$x]);
                            $object3 = (object) [
                                'optionid2' => $changuevaluess[0],
                                'lastinsert2' => $changuevaluess[1],
                                'optionval2' => $changuevaluess[2],
                                'optionid3' => $changuevaluess[3],
                                'lastinsert3' => $changuevaluess[4],
                                'optionval3' => $changuevaluess[5],    
                            ];
                            array_push($arrayData3, $object3);
                        }
                        //return response()->json($arrayData3);
                        $ultimovalorcontador = DB::select( DB::raw('SELECT MAX(id) as id FROM optionvaluemix WHERE idvideo = "'.$idvideo.'";') );
                        $contadorpareja2 = $ultimovalorcontador[0]->id;
                        $datareal = $arrayData3;
                        for($k = 0; $k<count($datareal); $k++){
                            $contadorpareja2 = $contadorpareja2 + 1;
                            $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "'.$price.'" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
        
                            $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid3.'","'.$datareal[$k]->lastinsert3.'", "'.$price.'" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
        
                        }
                        return response()->json(200);
                    }else if($contadordeopciones == 3){
                        $optionsvalid2 = '';
                        $arrayData2 = array();
                        $arrayParacomparar = array();
                        
                        for($i = 0; $i<count($arrayData); $i++){ 
                            $valork = 0;

                            if($arrayData[$j]->optionid != $valork){



                                
                            }


                                for($j = 0; $j<count($option); $j++){ 
                                    $valork = $arrayData[$k]->optionid;
                                    for($m = 0; $m<count($option); $m++){
                                        if($arrayData[$i]->optionid != $option[$j]['optionkeyid'] && $arrayData[$i]->optionid != $option[$m]['optionkeyid'] && $option[$j]['optionkeyid'] != $option[$m]['optionkeyid']){
                                            $optionsval2 = explode(",", $option[$j]['optionval']);
                                            $optionsvalid2 = explode(",", $option[$j]['optionvalid']);
                                            // otra option
                                            $optionsval2_1 = explode(",", $option[$m]['optionval']);
                                            $optionsvalid2_1 = explode(",", $option[$m]['optionvalid']);
                                            //agregar optionvaluemix
                                            for($k = 0; $k<count($optionsvalid2); $k++){
                                                // reemplazar valores
                                                $myreplace = $optionsvalid2[$k];
                                                if($optionsvalid2[$k] == 0){
                                                    for($x = 0; $x<count($arrayData); $x++){
                                                        if($optionsval2[$k] == $arrayData[$x]->optionval){
                                                            $myreplace = $arrayData[$x]->lastinsert;
                                                        }
                                                    }
                                                }
                                                // ingresa a la condicion
                                                if($j == 0){
                                                    array_push($arrayParacomparar, intval($option[$j]['optionkeyid']).'@#@'.intval($myreplace).'@#@'.$optionsval2[$k].'@#@'.intval($arrayData[$i]->optionid).'@#@'.intval($arrayData[$i]->lastinsert).'@#@'.$arrayData[$i]->optionval.'@#@---'.$optionsval2_1[$k]);
                                                }else{
                                                    array_push($arrayParacomparar, intval($arrayData[$i]->optionid).'@#@'.intval($arrayData[$i]->lastinsert).'@#@'.$arrayData[$i]->optionval.'@#@'.intval($option[$j]['optionkeyid']).'@#@'.intval($myreplace).'@#@'.$optionsval2[$k].'@#@----'.$optionsval2_1[$k]);
                                                }
                                            }
                                        }
                                    }
                                }





                        }
                        $arrayaconvertir = array_values(array_unique($arrayParacomparar));
                        return response()->json($arrayaconvertir);
                        $arrayData3 = array();
                        for($x = 0; $x<count($arrayaconvertir); $x++){
                            $changuevaluess = explode("@#@", $arrayaconvertir[$x]);
                            $object3 = (object) [
                                'optionid2' => $changuevaluess[0],
                                'lastinsert2' => $changuevaluess[1],
                                'optionval2' => $changuevaluess[2],
                                'optionid3' => $changuevaluess[3],
                                'lastinsert3' => $changuevaluess[4],
                                'optionval3' => $changuevaluess[5],    
                            ];
                            array_push($arrayData3, $object3);
                        }
                        $ultimovalorcontador = DB::select( DB::raw('SELECT MAX(id) as id FROM optionvaluemix WHERE idvideo = "'.$idvideo.'";') );
                        $contadorpareja2 = $ultimovalorcontador[0]->id;
                        $datareal = $arrayData3;
                        for($k = 0; $k<count($datareal); $k++){
                            $contadorpareja2 = $contadorpareja2 + 1;
                            $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "'.$price.'" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
        
                            $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid3.'","'.$datareal[$k]->lastinsert3.'", "'.$price.'" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
        
                        }
                        return response()->json(200);
                    }
                }else{

                }
            }
        }
    }
    //
    public function addoptions(Request $request){
        $request->validate([
            'idvideo' => 'required', 
            'payment_type' => 'required',
            'option' => 'required'
        ]);
        $idvideo = $request->idvideo;
        $payment_type = $request->payment_type;
        $option = $request->option;
        $price = 0;
        $contadorpareja = 0;
        $arrayData = array();
        $validateoptions = DB::select( DB::raw('select distinct optionvaluemix.idvideo from optionvaluemix where optionvaluemix.idvideo = "'.$idvideo.'";'));
        //return response()->json(count($option));
        if(count($validateoptions) != 0){
            $banderaelima = false;
            $esinsertoupdate = false;
            if($banderaelima == false){
                $index = -1;
                foreach ($option as $opt) {
                    $index++;
                    $optionkeyid = $opt['optionkeyid'];
                    $optionvalid = explode(",", $opt['optionvalid']);
                    $optionsval = explode(",", $opt['optionval']);
                    $optionsvalcompare = explode(",", $opt['optionval']);
                    for($i = 0; $i<count($optionvalid); $i++){
                        if($optionvalid[$i] == 0){

                            //return response()->json(key($optionvalid));
                            /*
                            unset($optionsvalcompare[$i]);
                            for($j = 0; $j<count($optionsvalcompare); $j++){
                                if(strtoupper($optionsval[$i]) == strtoupper($optionsvalcompare[$j])){
                                    return response()->json([
                                        'status' => 401,
                                        'message' => $optionsval[$i].' se repite',
                                      ], 401);
                                }
                            }*/
                            $results2 = DB::select( DB::raw('INSERT INTO optionvalue (id, idoption, descripcion, id_subcription, created_at, updated_at) VALUES (NULL, "'.$optionkeyid.'", "'.$optionsval[$i].'", NULL, now(),now());') );
                            $id2 = DB::getPdo()->lastInsertId();
                            $object = (object) [
                                'optionid' => $optionkeyid,
                                'lastinsert' => $id2,
                                'optionval' => $optionsval[$i],
                                'optionkey' => count($option),
                            ];
                            array_push($arrayData, $object);

                        }
                    }
                    $esinsertoupdate = true;
                }
                //return response()->json($arrayData);
                $arrayParacomparar = array();
                if($esinsertoupdate == true){
                    $optionsvalid2 = '';
                    $arrayData2 = array();
                    for($i = 0; $i<count($arrayData); $i++){ 
                        for($j = 0; $j<count($option); $j++){ 
                            if($arrayData[$i]->optionid != $option[$j]['optionkeyid']){
                                $optionsval2 = explode(",", $option[$j]['optionval']);
                                $optionsvalid2 = explode(",", $option[$j]['optionvalid']);
                                //agregar optionvaluemix
                                for($k = 0; $k<count($optionsvalid2); $k++){
                                    // reemplazar valores
                                    $myreplace = $optionsvalid2[$k];
                                    if($optionsvalid2[$k] == 0){
                                        for($x = 0; $x<count($arrayData); $x++){
                                            if($optionsval2[$k] == $arrayData[$x]->optionval){
                                                $myreplace = $arrayData[$x]->lastinsert;
                                            }
                                        }
                                    }
                                    // ingresa a la condicion
                                    if($j == 0){
                                        array_push($arrayParacomparar, intval($option[$j]['optionkeyid']).'@#@'.intval($myreplace).'@#@'.$optionsval2[$k].'@#@'.intval($arrayData[$i]->optionid).'@#@'.intval($arrayData[$i]->lastinsert).'@#@'.$arrayData[$i]->optionval);
                                    }else{
                                        array_push($arrayParacomparar, intval($arrayData[$i]->optionid).'@#@'.intval($arrayData[$i]->lastinsert).'@#@'.$arrayData[$i]->optionval.'@#@'.intval($option[$j]['optionkeyid']).'@#@'.intval($myreplace).'@#@'.$optionsval2[$k]);
                                    }
                                }
                            }
                        }
                    }
                    $arrayaconvertir = array_values(array_unique($arrayParacomparar));
                    $arrayData3 = array();
                    for($x = 0; $x<count($arrayaconvertir); $x++){
                        $changuevaluess = explode("@#@", $arrayaconvertir[$x]);
                        $object3 = (object) [
                            'optionid2' => $changuevaluess[0],
                            'lastinsert2' => $changuevaluess[1],
                            'optionval2' => $changuevaluess[2],
                            'optionid3' => $changuevaluess[3],
                            'lastinsert3' => $changuevaluess[4],
                            'optionval3' => $changuevaluess[5],    
                        ];
                        array_push($arrayData3, $object3);
                    }
                    //return response()->json($arrayData3);
                    $ultimovalorcontador = DB::select( DB::raw('SELECT MAX(id) as id FROM optionvaluemix WHERE idvideo = "'.$idvideo.'";') );
                    $contadorpareja2 = $ultimovalorcontador[0]->id;
                    $datareal = $arrayData3;
                    for($k = 0; $k<count($datareal); $k++){
                        $contadorpareja2 = $contadorpareja2 + 1;
                        $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "'.$price.'" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
    
                        $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid3.'","'.$datareal[$k]->lastinsert3.'", "'.$price.'" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
    
                    }
                    return response()->json(200);
                }else{

                }
            }
        }
    }
    //
    public function updateOptionsValues0(Request $request){
        
        $request->validate([
            'videoprofile' => 'required', 
            'idvideo' => 'required', 
            'options_keys' => 'required',
            'options_values' => 'required'
        ]);
        $videoprofile = $request->videoprofile;
        $idvideo = $request->idvideo;
        $options_keys = $request->options_keys;
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
                'precio' => 0,
            ];
            array_push($ultimoresultado, $model1);
        }
        //return response()->json($ultimoresultado);
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
        //return response()->json($ultimoresultado);
        for($i = 0; $i<count($optionkey); $i++){
            $model1_1 = (object) [
                'optionkey' => $optionkey[$i],
                'optionval' => $optionval[$i],
                'precio' => 0,
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
                    'precio' => 0,
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
                        'precio2' => 0,
                        'optionval3' => $paratesteo[$j]->optionval,
                        'precio3' => 0,
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
                $updatevideo = DB::select( DB::raw('UPDATE videos SET titlevideo = "'.$videoprofile[0]['titlevideo'].'", VideoDescription = "'.$videoprofile[0]['VideoDescription'].'" ,urlimagen = "'.$videoprofile[0]['urlimagen'].'", idpmtype = "'.$videoprofile[0]['idpmtype'].'", updated_at = now() WHERE videos.id = "'.$idvideo.'";') );


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
                            'precio' => 0,
                            'optionkey' => count($optionsval),
                        ];
                        array_push($arrayData, $object);

                    }
                }
                $arrayData2 = array();
                for($i = 0; $i<count($arrayData); $i++){
                    for($j = 0; $j<count($arrayData); $j++){
                        if($arrayData[$i]->optionid != $arrayData[$j]->optionid){

                            for($z = 0; $z<count($options_values); $z++){
                                $explotandodesc = explode(" / ",$options_values[$z]['descripcion']);
        
                                if($explotandodesc[0] == $arrayData[$i]->optionval){
                                    if($explotandodesc[1] == $arrayData[$j]->optionval){
                                        $imagenfinal = $options_values[$z]['img'];
                                        $preciofinal = $options_values[$z]['precio'];
                                        $sortfinal = $options_values[$z]['sort'];

                                        $object2 = (object) [
                                            'optionid2' => $arrayData[$i]->optionid,
                                            'lastinsert2' => $arrayData[$i]->lastinsert,
                                            'optionval2' => $arrayData[$i]->optionval,
                                            'imagenfinal' => $imagenfinal,
                                            'preciofinal' => $preciofinal,
                                            'sortfinal' => $sortfinal,
                                            'optionid3' => $arrayData[$j]->optionid,
                                            'lastinsert3' => $arrayData[$j]->lastinsert,
                                            'optionval3' => $arrayData[$j]->optionval,
                                            //'precio3' => $arrayData[$j]->precio,
                                        ];
                                        array_push($arrayData2, $object2);


                                    }else{
                                        $imagenfinal = 'defect';
                                    }
                                }
                            }
                        }
                    }
                }

                $contadorpareja2 = 0;
                $datareal = $arrayData2;

                //return response()->json($datareal);
                for($k = 0; $k<count($datareal); $k++){
                    
                    $contadorpareja2 = $contadorpareja2 + 1;
                    $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "'.$datareal[$k]->preciofinal.'" , "'.$datareal[$k]->imagenfinal.'", NULL,NULL,NULL, "'.$datareal[$k]->sortfinal.'" ,now(),now());') );

                    $results4 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid3.'","'.$datareal[$k]->lastinsert3.'", "'.$datareal[$k]->preciofinal.'" , "'.$datareal[$k]->imagenfinal.'", NULL,NULL,NULL, "'.$datareal[$k]->sortfinal.'" ,now(),now());') );
                }
                return response()->json(200);
            }
        }else{
            return response()->json(false);
        }
        
    }
    
}
