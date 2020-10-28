<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Subscription;
use App\Config;

class UpdateOptionsValuesController extends Controller
{
    public function updatevideopublic(Request $request){
        $request->validate([
            'idvideo' => 'required', 
            'public' => 'required',
        ]);
        $idvideo = $request->idvideo;
        $public = $request->public;



        $results = DB::select( DB::raw('SELECT urlvideo FROM videos WHERE videos.id= "'.$idvideo.'";'));
        
        if($results[0]->urlvideo != ''){
            $firstexplode = explode("external/", $results[0]->urlvideo);
            $firstexplode = explode(".", $firstexplode[1]);
            $updatevideo = DB::select( DB::raw('UPDATE videos SET public = "'.$public.'", updated_at = now() WHERE videos.id = "'.$idvideo.'";') );

            $validareview = DB::select( DB::raw('SELECT id,status_video,urlvideo FROM reviewvideo WHERE reviewvideo.idvideo= "'.$idvideo.'";'));

            //buscar categoria
            $buscarcategoria = DB::select( DB::raw('SELECT videos.userId,serviciousuarios.serviciosId FROM videos INNER JOIN serviciousuarios ON serviciousuarios.userId = videos.userId WHERE videos.id = "'.$idvideo.'";'));
            $serviciosId = $buscarcategoria[0]->serviciosId;

            if($public == 21){

                 
                 //sumar categoria
                $sumarvideocategoria = DB::select( DB::raw('UPDATE servicios SET nvideos = nvideos+1, updated_at = now() WHERE servicios.id = "'.$serviciosId.'";') );


                if($validareview){
                    $firstexplode2 = explode("external/", $validareview[0]->urlvideo);
                    $firstexplode2 = explode(".", $firstexplode2[1]);
                    if($firstexplode[0] != $firstexplode2[0]){
                        $updatereview = DB::select( DB::raw('UPDATE reviewvideo SET status_video = 0, urlvideo = "'.$results[0]->urlvideo.'", updated_at = now() WHERE reviewvideo.idvideo= "'.$idvideo.'";') );

                        return response()->json([
                            200 => 'OK 1',
                          ], 200);
                    }else{
                        return response()->json([
                            200 => 'OK 2',
                          ], 200);
                    }
                }else{
                    $insertreview = DB::select( DB::raw('INSERT INTO reviewvideo (id, idvideo, status_video,urlvideo, created_at, updated_at) VALUES (NULL, "'.$idvideo.'", 0,"'.$results[0]->urlvideo.'", now(), now());') ); 
                    return response()->json([
                        200 => 'OK 3',
                      ], 200);
                }
            }else{
                //restar categoria
                $restarvideocategoria = DB::select( DB::raw('UPDATE servicios SET nvideos = nvideos-1, updated_at = now() WHERE servicios.id = "'.$serviciosId.'";') );

                if($validareview){
                    if($validareview[0]->status_video == 0){
                        $eliminareview = DB::table('reviewvideo')
                            ->where('idvideo',$idvideo)
                            ->delete();
                        if($eliminareview){
                            return response()->json([
                                200 => 'OK 4',
                              ], 200);
                        }    
                    }else{
                        return response()->json([
                            200 => 'OK 5',
                          ], 200);
                    }
                }
            }
            
        }else{
            $resultsreview = DB::select( DB::raw('SELECT status_video FROM reviewvideo WHERE reviewvideo.idvideo= "'.$idvideo.'" and status_video = 2;'));
            if($resultsreview){
                return response()->json([
                    412 => 'Precondition Failed',
                    'message' => 'Video no disponible por que fue eliminado por infringir las reglas, suba otro video por favor',
                  ], 412);
            }else{
                return response()->json([
                    406 => 'Not Acceptable',
                    'message' => 'Video procesando, aún no puedes publicar',
                  ], 406);
            }
        }   
    }
    //
    public function addoptionvaluemix(Request $request){
        $request->validate([
            'videoid' => 'required', 
            'payment_type' => 'required',
            'option' => 'required'
        ]);
        $idvideo = $request->videoid;
        $payment_type = $request->payment_type;
        $option = $request->option;
        $price = 0;
        $contadorpareja = 0;
        $arrayData = array();
        $arrayData_new_p = array();
        $validateoptions = DB::select( DB::raw('select distinct optionvaluemix.idvideo from optionvaluemix where optionvaluemix.idvideo = "'.$idvideo.'";'));
        $contadordeopciones = count($option);
        
        if(count($validateoptions) != 0){
            $esinsertoupdate = false;
            $index = -1;
            foreach ($option as $opt) {
                $index++;
                $optionkey = $opt['optionkey'];
                $optionkeyid = $opt['optionkeyid'];
                $optionvalid = explode(",", $opt['optionvalid']);
                $optionsval = explode(",", $opt['optionval']);
                $optionsvalcompare = explode(",", $opt['optionval']);
                
                if($opt['optionkeyid'] == 0){

                    $results_new_p = DB::select( DB::raw('INSERT INTO options (id, idvideo, descripcion, created_at, updated_at) VALUES (NULL, "'.$idvideo.'", "'.$optionkey.'", now(), now());') ); 
                    $id_new_p = DB::getPdo()->lastInsertId();

                    for($i = 0; $i<count($optionvalid); $i++){
                        $results2 = DB::select( DB::raw('INSERT INTO optionvalue (id, idoption, descripcion, id_subcription, created_at, updated_at) VALUES (NULL, "'.$id_new_p.'", "'.$optionsval[$i].'", NULL, now(),now());') );
                        $id2_new_p = DB::getPdo()->lastInsertId();
                        $object_new_p = (object) [
                            'optionid' => $id_new_p,
                            'lastinsert' => $id2_new_p,
                            'optionval' => $optionsval[$i],
                            'optionkey' => count($option),
                        ];
                        array_push($arrayData_new_p, $object_new_p);
                    }
                    //return response()->json($arrayData_new_p);
                }else{
                    for($i = 0; $i<count($optionvalid); $i++){
                        if($optionvalid[$i] == 0){
                            $results2 = DB::select( DB::raw('INSERT INTO optionvalue (id, idoption, descripcion, id_subcription, created_at, updated_at) VALUES (NULL, "'.$optionkeyid.'", "'.$optionsval[$i].'", NULL, now(),now());') );
                            $id2 = DB::getPdo()->lastInsertId();
                            $object = (object) [
                                'optionid' => strval($optionkeyid),
                                'lastinsert' => $id2,
                                'optionval' => $optionsval[$i],
                                'optionkey' => count($option),
                            ];
                            array_push($arrayData, $object);
                        }
                    }
                }
                if($arrayData_new_p){
                    $arrayData = array_merge($arrayData, $arrayData_new_p);
                }
                $esinsertoupdate = true;
            }
            
            if($esinsertoupdate == true){
                //return response()->json($arrayData);
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
                            if(count($arrayData_new_p) == 0){
                                $valorantiguo = $arrayData[$i]->optionid;
                            }
                            for($k = 0; $k<count($arrayData_new_p); $k++){
                                if($arrayData_new_p[$k]->optionid == $arrayData[$i]->optionid){
                                    $valorantiguo = 0;
                                }else{
                                    $valorantiguo = $arrayData[$i]->optionid;
                                }
                            }
                            
                            if($valorantiguo != $option[$j]['optionkeyid']){    
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
                    //return response()->json($arrayaconvertir);
                    

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
            
                    $recuperardata = DB::select( DB::raw('SELECT COUNT(*) as contar,idoptionvalue,precio,img,id_item_stripe,interval_stripe,interval_count_stripe FROM optionvaluemix WHERE idvideo = "'.$idvideo.'" GROUP BY id;') );
                    $recupera_arrayData = array();
                    //return response()->json($arrayData3);
                    $reiniciarcontadorpareja = false;
                    if(count($recuperardata) != 0){
                        if($recuperardata[0]->contar == 1){
                            $reiniciarcontadorpareja = true;
                            for($p = 0; $p<count($recuperardata); $p++){
                                $recupera_object = (object) [
                                    'recupera_idoptionvalue' => $recuperardata[$p]->idoptionvalue,
                                    'recupera_precio' => $recuperardata[$p]->precio,
                                    'recupera_img' => $recuperardata[$p]->img,
                                    'recupera_id_item_stripe' => $recuperardata[$p]->id_item_stripe,
                                    'recupera_interval_stripe' => $recuperardata[$p]->interval_stripe,
                                    'recupera_interval_count_stripe' => $recuperardata[$p]->interval_count_stripe,    
                                ];
                                array_push($recupera_arrayData, $recupera_object);

                            }
                        }else if($recuperardata[0]->contar == 2){
                            $reiniciarcontadorpareja = false;
                        }
                    }

                    
                    if($reiniciarcontadorpareja == true){
                        $contadorpareja2 = 0;
                        $eliminaoptionvaluemix = DB::table('optionvaluemix')
                        ->where('optionvaluemix.idvideo',$idvideo)
                        ->delete();
                    }else{
                        $ultimovalorcontador = DB::select( DB::raw('SELECT MAX(id) as id FROM optionvaluemix WHERE idvideo = "'.$idvideo.'";') );
                        $contadorpareja2 = $ultimovalorcontador[0]->id;
                    }

                    $datareal = $arrayData3;

                    
                    for($k = 0; $k<count($datareal); $k++){

                        if($datareal[$k]->optionid3 != 0 ){
                            $contadorpareja2 = $contadorpareja2 + 1;

                            $precio_oficial1 = 0;
                            $imagen_oficial1 = null;
                            $id_item_stripe_oficial1 = null;
                            $interval_stripe_oficial1 = null;
                            $interval_count_stripe_oficial1 = null;

                            for($o = 0; $o<count($recupera_arrayData); $o++){
                                if($recupera_arrayData[$o]->recupera_idoptionvalue == $datareal[$k]->lastinsert2 ){
                                    $precio_oficial1 = $recupera_arrayData[$o]->recupera_precio;
                                    $imagen_oficial1 = $recupera_arrayData[$o]->recupera_img;
                                    $id_item_stripe_oficial1 = $recupera_arrayData[$o]->recupera_id_item_stripe;
                                    $interval_stripe_oficial1 = $recupera_arrayData[$o]->recupera_interval_stripe;
                                    $interval_count_stripe_oficial1 = $recupera_arrayData[$o]->recupera_interval_count_stripe;
                                }
                                if( $recupera_arrayData[$o]->recupera_idoptionvalue == $datareal[$k]->lastinsert3){
                                    $precio_oficial1 = $recupera_arrayData[$o]->recupera_precio;
                                    $imagen_oficial1 = $recupera_arrayData[$o]->recupera_img;
                                    $id_item_stripe_oficial1 = $recupera_arrayData[$o]->recupera_id_item_stripe;
                                    $interval_stripe_oficial1 = $recupera_arrayData[$o]->recupera_interval_stripe;
                                    $interval_count_stripe_oficial1 = $recupera_arrayData[$o]->recupera_interval_count_stripe;
                                }
                                
                            }

                            $results3 = DB::table('optionvaluemix')
                                ->insert([
                                'n' => null,
                                'id' => $contadorpareja2,
                                'idvideo' => $idvideo,
                                'idoption' => $datareal[$k]->optionid2,
                                'idoptionvalue' => $datareal[$k]->lastinsert2,
                                'precio' => $precio_oficial1,
                                'img' => $imagen_oficial1,
                                'id_item_stripe' => $id_item_stripe_oficial1,
                                'interval_stripe' => $interval_stripe_oficial1,
                                'interval_count_stripe' => $interval_count_stripe_oficial1,
                                'sort' => $contadorpareja2,
                            ]);

                            $results4 = DB::table('optionvaluemix')
                                ->insert([
                                'n' => null,
                                'id' => $contadorpareja2,
                                'idvideo' => $idvideo,
                                'idoption' => $datareal[$k]->optionid3,
                                'idoptionvalue' => $datareal[$k]->lastinsert3,
                                'precio' => $precio_oficial1,
                                'img' => $imagen_oficial1,
                                'id_item_stripe' => $id_item_stripe_oficial1,
                                'interval_stripe' => $interval_stripe_oficial1,
                                'interval_count_stripe' => $interval_count_stripe_oficial1,
                                'sort' => $contadorpareja2,
                            ]);
                        }

                    }
                    return response()->json(200);



                }else if($contadordeopciones == 3){
                    $optionsvalid2 = '';
                    $arrayData2 = array();
                    $arrayParacomparar = array();
                    for($i = 0; $i<count($arrayData); $i++){ 

                        for($j = 0; $j<count($option); $j++){ 

                            if(count($arrayData_new_p) == 0){
                                $valorantiguo = $arrayData[$i]->optionid;
                            }
                            for($k = 0; $k<count($arrayData_new_p); $k++){
                                if($arrayData_new_p[$k]->optionid == $arrayData[$i]->optionid){
                                    $valorantiguo = 0;
                                }else{
                                    $valorantiguo = $arrayData[$i]->optionid;
                                }
                            }

                            if($valorantiguo != $option[$j]['optionkeyid']){

                                for($m = 0; $m<count($option); $m++){

                                    if(count($arrayData_new_p) == 0){
                                        $valorantiguo2 = $arrayData[$j]->optionid;
                                    }
                                    for($k = 0; $k<count($arrayData_new_p); $k++){
                                        if($arrayData_new_p[$k]->optionid == $arrayData[$j]->optionid){
                                            $valorantiguo2 = 0;
                                        }else{
                                            $valorantiguo2 = $arrayData[$j]->optionid;
                                        }
                                    }

                                    if($valorantiguo2 != $option[$m]['optionkeyid']){
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
                                            for($b = 0; $b<count($optionsvalid2_1); $b++){
                                                $myreplace2 = $optionsvalid2_1[$b];
                                                if($optionsvalid2_1[$b] == 0){
                                                    for($x = 0; $x<count($arrayData); $x++){
                                                        if($optionsval2_1[$b] == $arrayData[$x]->optionval){
                                                            $myreplace2 = $arrayData[$x]->lastinsert;
                                                        }
                                                    }
                                                }
                                                // ingresa a la condicion
                                                /*
                                                if($j == 0 && $m == 0){
                                                    array_push($arrayParacomparar, intval($option[$j]['optionkeyid']).'@#@'.intval($myreplace).'@#@'.$optionsval2[$k].'@#@'.intval($arrayData[$i]->optionid).'@#@'.intval($arrayData[$i]->lastinsert).'@#@'.$arrayData[$i]->optionval.'@#@---'.$optionsval2_1[$k].'@#@---'.$optionsvalid2_1[$k].'@#@---'.$myreplace2);
                                                }else{
                                                    array_push($arrayParacomparar, intval($arrayData[$i]->optionid).'@#@'.intval($arrayData[$i]->lastinsert).'@#@'.$arrayData[$i]->optionval.'@#@'.intval($option[$j]['optionkeyid']).'@#@'.intval($myreplace).'@#@'.$optionsval2[$k].'@#@----'.$optionsval2_1[$k].'@#@---'.$optionsvalid2_1[$k].'@#@---'.$myreplace2);
                                                }*/
                                                if($j == 0){
                                                    if($arrayData[$i]->lastinsert != $arrayData[$m]->lastinsert && $option[$j]['optionkeyid'] != $option[$m]['optionkeyid']){
                                                       
                                                        array_push($arrayParacomparar, 
                                                    
                                                            intval($option[$j]['optionkeyid']).'@#@'.
                                                            intval($myreplace).'@#@'.
                                                            $optionsval2[$k].'@#@'.
                                                            intval($arrayData[$i]->optionid).'@#@'.
                                                            intval($arrayData[$i]->lastinsert).'@#@'.
                                                            $arrayData[$i]->optionval.'@#@'.
                                                    
                                                    
                                                            intval($option[$m]['optionkeyid']).'@#@'.
                                                            intval($myreplace2).'@#@'.
                                                            $optionsval2_1[$k]
                                                            
                                                        );
                                                    }
                                                    
                                                }else{
                                                    if($arrayData[$i]->lastinsert != $arrayData[$m]->lastinsert && $option[$j]['optionkeyid'] != $option[$m]['optionkeyid'] ){
                                                        array_push($arrayParacomparar,
                                                    

                                                            intval($option[$m]['optionkeyid']).'@#@'.
                                                            intval($myreplace2).'@#@'.
                                                            $optionsval2_1[$k].'@#@'.
                                                        
                                                            
                                                            intval($arrayData[$i]->optionid).'@#@'.
                                                            intval($arrayData[$i]->lastinsert).'@#@'.
                                                            $arrayData[$i]->optionval.'@#@'.
                                                            intval($option[$j]['optionkeyid']).'@#@'.
                                                            intval($myreplace).'@#@'.
                                                            $optionsval2[$k]
                                                        );
                                                    }
                                                    
                                                }
    
                                            }
                                            
                                            
                                        }
                                    }
                                }
    
                                
/*
                                if($arrayData[$i]->optionid != $option[$j]['optionkeyid'] && $arrayData[$i]->optionid != $option[$m]['optionkeyid'] && $option[$j]['optionkeyid'] != $option[$m]['optionkeyid']){
                                    
                                }*/


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

        }else{

            
            $validatevideomodelo = DB::select( DB::raw('select distinct videomodelo.id from videomodelo where videomodelo.idvideo = "'.$idvideo.'";'));
            
            if(count($validatevideomodelo) == 0){
                $insertvideomodelo = DB::select( DB::raw('INSERT INTO videomodelo (id, idvideo, idmodelo, created_at, updated_at) VALUES (NULL, "'.$idvideo.'", 1, now(), now());') ); 
            }else{
                $eliminaoption = DB::table('videomodelo')
                    ->where('videomodelo.idvideo',$idvideo)
                    ->delete();
                if($eliminaoption){
                    $insertvideomodelo = DB::select( DB::raw('INSERT INTO videomodelo (id, idvideo, idmodelo, created_at, updated_at) VALUES (NULL, "'.$idvideo.'", 1, now(), now());') ); 
                }    
            }


            $validatonlyoption = DB::select( DB::raw('select distinct idvideo from options where idvideo = "'.$idvideo.'";'));


            if(count($validatonlyoption) != 0){
                $validaop = DB::select( DB::raw('select distinct idvideo from options inner join optionvalue ON optionvalue.idoption = options.id where idvideo = "'.$idvideo.'";'));
                if(count($validaop) != 0){
                    $eliminaoptionvalue = DB::table('optionvalue')
                    ->join('options', 'options.id', '=', 'optionvalue.idoption')
                    ->where('options.idvideo',$idvideo)
                    ->delete();
                    if($eliminaoptionvalue){
                        $eliminaoption = DB::table('options')
                            ->where('options.idvideo',$idvideo)
                            ->delete();
                    }
                }else{
                    $eliminaoption = DB::table('options')
                            ->where('options.idvideo',$idvideo)
                            ->delete();
                }  
            }


            $contadoropcions = count($option);
            $updateidmtype = DB::select( DB::raw('UPDATE videos SET idpmtype = "'.$payment_type.'", updated_at = now() WHERE videos.id = "'.$idvideo.'";') );

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
                    $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
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
                    $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
    
                    $results4 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid3.'","'.$datareal[$k]->lastinsert3.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
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
                    $results3 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid2.'","'.$datareal[$k]->lastinsert2.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
    
                    $results4 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid3.'","'.$datareal[$k]->lastinsert3.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );

                    $results5 = DB::select( DB::raw('INSERT INTO optionvaluemix (n,id,idvideo,idoption,idoptionvalue, precio,img,id_item_stripe,interval_stripe,interval_count_stripe,sort,created_at,updated_at) VALUES (NULL, "'.$contadorpareja2.'", "'.$idvideo.'", "'.$datareal[$k]->optionid4.'","'.$datareal[$k]->lastinsert4.'", "0" , NULL, NULL,NULL,NULL, "'.$contadorpareja2.'" ,now(),now());') );
                }
            }
            return response()->json(200);





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
    
    public function updateStripetest(){
        return response()->json(Config::VimeoConfig()['client_secret']);
        $id_item_stripe = 'price_0HVPCIOHap96YKlCfHLNTXBN';
        $preciofinal = 4500;
        $id_product_stripe = 'prod_I5aMfPnzk1enX3';
        Stripe::setApiKey(Config::StripeConfig()['StripeApiKey']);
        
        $price = Price::update(
            $id_item_stripe,
            array(
                'active' => false
            )
        );
/*
        $price = Price::create(
            array(
                'unit_amount' => 9000,
                'currency' => 'usd',
                'recurring' => ['interval' => 'month'],
                'product' => $id_product_stripe
            )
        );*/
        
/*
        $product = Product::create(array(
            'name' => 'producto para borrar sin precio',
            'type' => 'service',
        ));*/
/*
        $stripe = new \Stripe\StripeClient(
            Config::StripeConfig()['StripeApiKey']
          );

        $stripe->products->delete(
            'prod_I5aMfPnzk1enX3',
            []
        );*/

        return response()->json($price);
    }                          
    public function updateOptionsValues0(Request $request){
        
        $request->validate([
            'videoprofile' => 'required', 
            'idvideo' => 'required', 
            //'options_keys' => 'required',
            //'options_values' => 'required'
        ]);
        $videoprofile = $request->videoprofile;
        $idvideo = $request->idvideo;
        $id_product_stripe = $request->id_product_stripe;
        $options_keys = $request->options_keys;
        $options_values = $request->options_values;

        $contadorpareja = 0;
        $arrayData = array();

        //
        $arrayTransformado = array();
        $arraymodelo1_1 = array();
        $arraymodelo1_2 = array();

        
        if($options_keys == null){

            if($id_product_stripe != null){
                return response()->json([
                    406 => 'Not Acceptable',
                    'message' => 'No se elimina, contiene Id Stripe',
                ], 406);
            }

            $updatevideo = DB::select( DB::raw('UPDATE videos SET titlevideo = "'.$videoprofile[0]['titlevideo'].'", VideoDescription = "'.$videoprofile[0]['VideoDescription'].'", idpmtype = "'.$videoprofile[0]['idpmtype'].'", updated_at = now() WHERE videos.id = "'.$idvideo.'";') );

            $validateoptions = DB::select( DB::raw('select distinct optionvaluemix.idvideo from optionvaluemix where optionvaluemix.idvideo = "'.$idvideo.'";'));

            if(count($validateoptions) != 0){
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
                            return response()->json(200);
                        }    
                    }
                }else{
                    return response()->json([
                        'status'  => 401,
                        'message' => 'Acceso no autorizado',
                    ], 401);
                }
            }else{
                return response()->json(200);
            }
        }

        $validateoptions = DB::select( DB::raw('select distinct optionvaluemix.idvideo from optionvaluemix where optionvaluemix.idvideo = "'.$idvideo.'";'));
        
        
        for($j = 0; $j<count($options_keys); $j++){   
            for($i = 0; $i<count($options_values); $i++){
                $optionsval = explode(" / ", $options_values[$i]['descripcion']);
                array_push($arrayTransformado, $optionsval[$j].'@#@'.$options_keys[$j]);
            }
        }
        //return response()->json($arrayTransformado);
        $arrayTransformado = array_values(array_unique($arrayTransformado));
        //return response()->json($arrayTransformado);
        $ultimoresultado = array();
        for($i = 0; $i<count($arrayTransformado); $i++){
            $mymodels1[$i] = explode("@#@", $arrayTransformado[$i]);
            $optionss = '';
            //$precioss = '';
            for($z = 0; $z<count($arrayTransformado); $z++){
                $mymodels2[$z] = explode("@#@", $arrayTransformado[$z]);
                if($mymodels1[$i][1] == $mymodels2[$z][1]){
                    if($optionss != ''){
                        $optionss = $mymodels2[$z][0].','.$optionss;
                        //$precioss = $mymodels2[$z][2].','.$precioss;
                    }else{
                        $optionss = $mymodels2[$z][0];
                        //$precioss = $mymodels2[$z][2];
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
        if(count($validateoptions) != 0){
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
                    }    
                }
            }else{
                return response()->json([
                    'status'  => 401,
                    'message' => 'Acceso no autorizado',
                ], 401);
            }

            if($banderaelima == true){
                $updatevideo = DB::select( DB::raw('UPDATE videos SET titlevideo = "'.$videoprofile[0]['titlevideo'].'", VideoDescription = "'.$videoprofile[0]['VideoDescription'].'" , idpmtype = "'.$videoprofile[0]['idpmtype'].'", updated_at = now() WHERE videos.id = "'.$idvideo.'";') );


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

                if(count($options_keys) == 1){
                    $arrayData2 = array();
                    $arrayData = array_reverse($arrayData);
                    for($i = 0; $i<count($arrayData); $i++){
                        for($z = 0; $z<count($options_values); $z++){
                            if($i == $z){

                                if (array_key_exists('img', $options_values[$i])) {
                                    $imagenfinal = $options_values[$i]['img'];
                                }else{
                                    $imagenfinal = null;
                                }

                                if (array_key_exists('id_item_stripe', $options_values[$i])) {
                                    $id_item_stripe = $options_values[$i]['id_item_stripe'];
                                }else{
                                    $id_item_stripe = null;
                                }

                                if (array_key_exists('interval_stripe', $options_values[$i])) {
                                    $interval_stripe = $options_values[$i]['interval_stripe'];
                                }else{
                                    $interval_stripe = null;
                                }

                                if (array_key_exists('interval_count_stripe', $options_values[$i])) {
                                    $interval_count_stripe = $options_values[$i]['interval_count_stripe'];
                                }else{
                                    $interval_count_stripe = null;
                                }

                                if (array_key_exists('count_stripe_customer', $options_values[$i])) {
                                    $count_stripe_customer = $options_values[$i]['count_stripe_customer'];
                                }else{
                                    $count_stripe_customer = null;
                                }

                                //$imagenfinal = $options_values[$i]['img'];
                                $preciofinal = $options_values[$i]['precio'];
                                $sortfinal = $options_values[$i]['sort'];
    
                                $object2 = (object) [
                                    'optionid2' => $arrayData[$i]->optionid,
                                    'lastinsert2' => $arrayData[$i]->lastinsert,
                                    'optionval2' => $arrayData[$i]->optionval,
                                    'imagenfinal' => $imagenfinal,
                                    'id_item_stripe' => $id_item_stripe,
                                    'interval_stripe' => $interval_stripe,
                                    'interval_count_stripe' => $interval_count_stripe,
                                    'count_stripe_customer' => $count_stripe_customer,
                                    'preciofinal' => $preciofinal,
                                    'sortfinal' => $sortfinal,
                                ];
                                array_push($arrayData2, $object2);
                            }
                        }
                    }
                    $contadorpareja2 = 0;
                    $datareal = $arrayData2;
                    //return response()->json($arrayData2);
                    for($k = 0; $k<count($datareal); $k++){
                        $contadorpareja2 = $contadorpareja2 + 1;





                        // add options stripe

                        if($id_product_stripe){
                            Stripe::setApiKey(Config::StripeConfig()['StripeApiKey']);
                            if($datareal[$k]->count_stripe_customer != 0){
                                // update status stripe
                                $price = Price::update(
                                    $datareal[$k]->id_item_stripe,
                                    array(
                                        'active' => true
                                    )
                                );
                            }else{
                                $price = Price::update(
                                    $datareal[$k]->id_item_stripe,
                                    array(
                                        'active' => false
                                    )
                                );
                            }
                        }

                        $results3 = DB::table('optionvaluemix')
                            ->insert([
                            'n' => null,
                            'id' => $contadorpareja2,
                            'idvideo' => $idvideo,
                            'idoption' => $datareal[$k]->optionid2,
                            'idoptionvalue' => $datareal[$k]->lastinsert2,
                            'precio' => $datareal[$k]->preciofinal,
                            'img' => $datareal[$k]->imagenfinal,
                            'id_item_stripe' => $datareal[$k]->id_item_stripe,
                            'interval_stripe' => $datareal[$k]->interval_stripe,
                            'interval_count_stripe' => $datareal[$k]->interval_count_stripe,
                            'count_stripe_customer' => $datareal[$k]->count_stripe_customer,
                            'sort' => $datareal[$k]->sortfinal,
                        ]);

                    }
                    return response()->json(200);

                }else if(count($options_keys) == 2){
                    $arrayData2 = array();
                    for($i = 0; $i<count($arrayData); $i++){
                        for($j = 0; $j<count($arrayData); $j++){
                            if($arrayData[$i]->optionid != $arrayData[$j]->optionid){
                                
                                for($z = 0; $z<count($options_values); $z++){
                                    $explotandodesc = explode(" / ",$options_values[$z]['descripcion']);
                                    
                                    if($explotandodesc[0] == $arrayData[$i]->optionval){
                                        if($explotandodesc[1] == $arrayData[$j]->optionval){
                                            //return response()->json($options_values[$z]['img']); 

                                            if (array_key_exists('img', $options_values[$z])) {
                                                $imagenfinal = $options_values[$z]['img'];
                                            }else{
                                                $imagenfinal = null;
                                            }

                                            if (array_key_exists('id_item_stripe', $options_values[$z])) {
                                                $id_item_stripe = $options_values[$z]['id_item_stripe'];
                                            }else{
                                                $id_item_stripe = null;
                                            }

                                            if (array_key_exists('interval_stripe', $options_values[$z])) {
                                                $interval_stripe = $options_values[$z]['interval_stripe'];
                                            }else{
                                                $interval_stripe = null;
                                            }

                                            if (array_key_exists('interval_count_stripe', $options_values[$z])) {
                                                $interval_count_stripe = $options_values[$z]['interval_count_stripe'];
                                            }else{
                                                $interval_count_stripe = null;
                                            }

                                            if (array_key_exists('count_stripe_customer', $options_values[$z])) {
                                                $count_stripe_customer = $options_values[$z]['count_stripe_customer'];
                                            }else{
                                                $count_stripe_customer = null;
                                            }

                                            
                                            $preciofinal = $options_values[$z]['precio'];
                                            $sortfinal = $options_values[$z]['sort'];

                                            $object2 = (object) [
                                                'optionid2' => $arrayData[$i]->optionid,
                                                'lastinsert2' => $arrayData[$i]->lastinsert,
                                                'optionval2' => $arrayData[$i]->optionval,
                                                'imagenfinal' => $imagenfinal,
                                                'id_item_stripe' => $id_item_stripe,
                                                'interval_stripe' => $interval_stripe,
                                                'interval_count_stripe' => $interval_count_stripe,
                                                'count_stripe_customer' => $count_stripe_customer,
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
                    for($k = 0; $k<count($datareal); $k++){
                        $contadorpareja2 = $contadorpareja2 + 1;

                        $results3 = DB::table('optionvaluemix')
                            ->insert([
                            'n' => null,
                            'id' => $contadorpareja2,
                            'idvideo' => $idvideo,
                            'idoption' => $datareal[$k]->optionid2,
                            'idoptionvalue' => $datareal[$k]->lastinsert2,
                            'precio' => $datareal[$k]->preciofinal,
                            'img' => $datareal[$k]->imagenfinal,
                            'id_item_stripe' => $datareal[$k]->id_item_stripe,
                            'interval_stripe' => $datareal[$k]->interval_stripe,
                            'interval_count_stripe' => $datareal[$k]->interval_count_stripe,
                            'count_stripe_customer' => $datareal[$k]->count_stripe_customer,
                            'sort' => $datareal[$k]->sortfinal,
                        ]);

                        $results4 = DB::table('optionvaluemix')
                            ->insert([
                            'n' => null,
                            'id' => $contadorpareja2,
                            'idvideo' => $idvideo,
                            'idoption' => $datareal[$k]->optionid3,
                            'idoptionvalue' => $datareal[$k]->lastinsert3,
                            'precio' => $datareal[$k]->preciofinal,
                            'img' => $datareal[$k]->imagenfinal,
                            'id_item_stripe' => $datareal[$k]->id_item_stripe,
                            'interval_stripe' => $datareal[$k]->interval_stripe,
                            'interval_count_stripe' => $datareal[$k]->interval_count_stripe,
                            'count_stripe_customer' => $datareal[$k]->count_stripe_customer,
                            'sort' => $datareal[$k]->sortfinal,
                        ]);
                        
                    }
                    return response()->json(200);

                }else if(count($options_keys) == 3){

                    /*
                    $eliminaoptionvalue = DB::table('optionvalue')
                    ->join('options', 'options.id', '=', 'optionvalue.idoption')
                    ->where('options.idvideo',$idvideo)
                    ->delete();
                    if($eliminaoptionvalue){
                        $eliminaoption = DB::table('options')
                            ->where('options.idvideo',$idvideo)
                            ->delete();
                        if($eliminaoption){
                            return response()->json([
                                'status' => 401,
                                'message' => 'Data eliminada, estamos trabajando...',
                              ], 401);
                        }    
                    }*/
                    //return response()->json($arrayData);
                    $arrayData2 = array();
                    $compararvalues = '000';
                    for($i = 0; $i<count($arrayData); $i++){
                        for($j = 0; $j<count($arrayData); $j++){
                            for($y = 0; $y<count($arrayData); $y++){
                                if($arrayData[$i]->optionid != $arrayData[$j]->optionid && $arrayData[$i]->optionid != $arrayData[$y]->optionid && $arrayData[$j]->optionid != $arrayData[$y]->optionid){

                                    for($z = 0; $z<count($options_values); $z++){


                                        $explotandodesc = explode(" / ",$options_values[$z]['descripcion']);
                                        
                                        if($explotandodesc[0] == $arrayData[$i]->optionval){
                                            if($explotandodesc[1] == $arrayData[$j]->optionval){
                                                if($explotandodesc[2] == $arrayData[$y]->optionval){
                                                //if($compararvalues != $i.$j.$y){
                                                    if (array_key_exists('img', $options_values[$z])) {
                                                        $imagenfinal = $options_values[$z]['img'];
                                                    }else{
                                                        $imagenfinal = null;
                                                    }
                                                    if (array_key_exists('id_item_stripe', $options_values[$z])) {
                                                        $id_item_stripe = $options_values[$z]['id_item_stripe'];
                                                    }else{
                                                        $id_item_stripe = null;
                                                    }
                                                    if (array_key_exists('interval_stripe', $options_values[$z])) {
                                                        $interval_stripe = $options_values[$z]['interval_stripe'];
                                                    }else{
                                                        $interval_stripe = null;
                                                    }
                                                    if (array_key_exists('interval_count_stripe', $options_values[$z])) {
                                                        $interval_count_stripe = $options_values[$z]['interval_count_stripe'];
                                                    }else{
                                                        $interval_count_stripe = null;
                                                    }
                                                    $preciofinal = $options_values[$z]['precio'];
                                                    $sortfinal = $options_values[$z]['sort'];

                                                    
                                                    $object2 = (object) [
                                                        'optionid2' => $arrayData[$i]->optionid,
                                                        'lastinsert2' => $arrayData[$i]->lastinsert,
                                                        'optionval2' => $arrayData[$i]->optionval,
                                                        'imagenfinal' => $imagenfinal,
                                                        'id_item_stripe' => $id_item_stripe,
                                                        'interval_stripe' => $interval_stripe,
                                                        'interval_count_stripe' => $interval_count_stripe,
                                                        'preciofinal' => $preciofinal,
                                                        'sortfinal' => $sortfinal,
                                                        'optionid3' => $arrayData[$j]->optionid,
                                                        'lastinsert3' => $arrayData[$j]->lastinsert,
                                                        'optionval3' => $arrayData[$j]->optionval,
                                                        'optionid4' => $arrayData[$y]->optionid,
                                                        'lastinsert4' => $arrayData[$y]->lastinsert,
                                                        'optionval4' => $arrayData[$y]->optionval,
                                                        '$i.$j.$y' => $i.$j.$y,
                                                        '$z' => $z,
                                                        'count i' => count($arrayData),
                                                        'count z' => count($options_values),
                                                    ];
                                                    array_push($arrayData2, $object2);
                                                }

                                                $compararvalues = $i.$j.$y;
                                            }else{
                                                $imagenfinal = 'defect';
                                            }
                                        }

                                    }

                                }

                            }

                            
                        }
                    }
                    /*
                    return response()->json([
                        'arrayData' => $arrayData,
                        'arrayData2' => $arrayData2,
                      ]);*/
                    $contadorpareja2 = 0;
                    $datareal = $arrayData2;
                    for($k = 0; $k<count($datareal); $k++){
                        $contadorpareja2 = $contadorpareja2 + 1;

                        $results3 = DB::table('optionvaluemix')
                            ->insert([
                            'n' => null,
                            'id' => $contadorpareja2,
                            'idvideo' => $idvideo,
                            'idoption' => $datareal[$k]->optionid2,
                            'idoptionvalue' => $datareal[$k]->lastinsert2,
                            'precio' => $datareal[$k]->preciofinal,
                            'img' => $datareal[$k]->imagenfinal,
                            'id_item_stripe' => $datareal[$k]->id_item_stripe,
                            'interval_stripe' => $datareal[$k]->interval_stripe,
                            'interval_count_stripe' => $datareal[$k]->interval_count_stripe,
                            'sort' => $datareal[$k]->sortfinal,
                        ]);

                        $results4 = DB::table('optionvaluemix')
                            ->insert([
                            'n' => null,
                            'id' => $contadorpareja2,
                            'idvideo' => $idvideo,
                            'idoption' => $datareal[$k]->optionid3,
                            'idoptionvalue' => $datareal[$k]->lastinsert3,
                            'precio' => $datareal[$k]->preciofinal,
                            'img' => $datareal[$k]->imagenfinal,
                            'id_item_stripe' => $datareal[$k]->id_item_stripe,
                            'interval_stripe' => $datareal[$k]->interval_stripe,
                            'interval_count_stripe' => $datareal[$k]->interval_count_stripe,
                            'sort' => $datareal[$k]->sortfinal,
                        ]);

                        $results5 = DB::table('optionvaluemix')
                            ->insert([
                            'n' => null,
                            'id' => $contadorpareja2,
                            'idvideo' => $idvideo,
                            'idoption' => $datareal[$k]->optionid4,
                            'idoptionvalue' => $datareal[$k]->lastinsert4,
                            'precio' => $datareal[$k]->preciofinal,
                            'img' => $datareal[$k]->imagenfinal,
                            'id_item_stripe' => $datareal[$k]->id_item_stripe,
                            'interval_stripe' => $datareal[$k]->interval_stripe,
                            'interval_count_stripe' => $datareal[$k]->interval_count_stripe,
                            'sort' => $datareal[$k]->sortfinal,
                        ]);
                        
                    }
                    return response()->json(200);

                    
                }

                
            }
        }else{
            $updatevideo = DB::select( DB::raw('UPDATE videos SET titlevideo = "'.$videoprofile[0]['titlevideo'].'", VideoDescription = "'.$videoprofile[0]['VideoDescription'].'", idpmtype = "'.$videoprofile[0]['idpmtype'].'", updated_at = now() WHERE videos.id = "'.$idvideo.'";') );

            $eliminaoptionvalue = DB::table('optionvalue')
            ->join('options', 'options.id', '=', 'optionvalue.idoption')
            ->where('options.idvideo',$idvideo)
            ->delete();
            if($eliminaoptionvalue){
                $eliminaoption = DB::table('options')
                    ->where('options.idvideo',$idvideo)
                    ->delete();
                if($eliminaoption){
                    return response()->json('Opciones eliminadas');
                }    
            }
        }
    }


    
}
