<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class OptionvaluemixController extends Controller{
    public function show(Request $request){
        $request->validate([
            'idvideo' => 'required',
        ]);
        $idvideo = $request->idvideo;    
        $results = DB::select( DB::raw('select videos.idpmtype,videos.titlevideo,optionvaluemix.idvideo as idvideo, optionvaluemix.id,options.id as option_keys_id, options.descripcion as option_keys,optionvalue.id as option_values_id, optionvalue.descripcion as option_values, optionvaluemix.precio, optionvaluemix.img, optionvaluemix.sort,optionvaluemix.id_item_stripe,optionvaluemix.interval_stripe,optionvaluemix.interval_count_stripe,videos.id_product_stripe from optionvaluemix INNER JOIN options ON options.id = optionvaluemix.idoption INNER JOIN optionvalue ON optionvalue.id = optionvaluemix.idoptionvalue INNER JOIN videos ON videos.id = optionvaluemix.idvideo where optionvaluemix.idvideo = "'.$idvideo.'" ORDER by optionvaluemix.sort ASC, options.id ASC, options.descripcion ASC'));

        if($results){
            $arrayData = array();
            $arrayData2 = array();
            $idvideo;
            $id_product_stripe;
            $mytitlevideo = '';
            for($i = 0; $i<count($results); $i++){
                $idvideo = $results[$i]->idvideo;
                $id_product_stripe = $results[$i]->id_product_stripe;
                $mytitlevideo = strip_tags($results[$i]->titlevideo);
                $object = (object) [
                    'idpmtype' => $results[$i]->idpmtype,
                    'id' => $results[$i]->id,
                    'id_product_stripe' => $results[$i]->id_product_stripe,
                    'option_keys' => $results[$i]->option_keys,
                    'option_keys_id' => $results[$i]->option_keys_id,
                    'option_values' => $results[$i]->option_values,
                    'option_values_id' => $results[$i]->option_values_id,
                    'precio' => $results[$i]->precio,
                    'img' => $results[$i]->img,
                    'id_item_stripe' => $results[$i]->id_item_stripe,
                    'interval_stripe' => $results[$i]->interval_stripe,
                    'interval_count_stripe' => $results[$i]->interval_count_stripe,
                    'titlevideo' => str_replace('<br>',' ',$results[$i]->titlevideo),
                    'sort' => $results[$i]->sort,
                ];
                array_push($arrayData, $object);
            }
            
            $idval=0;
            $idval_2=0;
            $descripcion = '';
            $descripcion_id = '';
            $precio = 0;
            $img = '';
            $titlevideo = '';
            $arrayData3 = array();

            // lista simple_1
            $arrayData4 = array();
            for($i = 0; $i<count($arrayData); $i++){
                array_push($arrayData4, $arrayData[$i]->option_keys);
            }
            $lista_simple = array_values(array_unique($arrayData4));
           
            // validando opciones 1 2 3
            if(count($lista_simple) == 1){
                for($i = 0; $i<count($arrayData); $i++){
                    $object3 = (object) [
                        'id' => $arrayData[$i]->id,
                        'descripcion' => $arrayData[$i]->option_values.'<br><small>'.$arrayData[$i]->titlevideo.'</small>',
                        'descripcion_id' => $arrayData[$i]->option_values_id,
                        'precio' => $arrayData[$i]->precio,
                        'img' => $arrayData[$i]->img,
                        'id_item_stripe' => $arrayData[$i]->id_item_stripe,
                        'interval_stripe' => $arrayData[$i]->interval_stripe,
                        'interval_count_stripe' => $arrayData[$i]->interval_count_stripe,
                    ];
                    array_push($arrayData3, $object3);
                }
            }
            if(count($lista_simple) == 2){
                for($i = 0; $i<count($arrayData); $i++){
                    if($arrayData[$i]->id == $idval){
                        $descripcion = $descripcion.' / '.$arrayData[$i]->option_values.'<br><small>'.$titlevideo.'</small>';
                        $descripcion_id = $descripcion_id.' / '.$arrayData[$i]->option_values_id;
                        $object3 = (object) [
                            'id' => $idval,
                            'descripcion' => $descripcion,
                            'descripcion_id' => $descripcion_id,
                            'precio' => $precio,
                            'img' => $img,
                            'id_item_stripe' => $id_item_stripe,
                            'interval_stripe' => $interval_stripe,
                            'interval_count_stripe' => $interval_count_stripe,
                        ];
                        array_push($arrayData3, $object3);
                    }
                    $idval = $arrayData[$i]->id;
                    $descripcion = $arrayData[$i]->option_values;
                    $descripcion_id = $arrayData[$i]->option_values_id;
                    $precio = $arrayData[$i]->precio;
                    $img = $arrayData[$i]->img;
                    $titlevideo = $arrayData[$i]->titlevideo;
                    $id_item_stripe = $arrayData[$i]->id_item_stripe;
                    $interval_stripe = $arrayData[$i]->interval_stripe;
                    $interval_count_stripe = $arrayData[$i]->interval_count_stripe;
                }
            }
            if(count($lista_simple) == 3){

                for($i = 0; $i<count($arrayData); $i++){
                    if($arrayData[$i]->id == $idval){
                        $descripcion = $descripcion.' / '.$arrayData[$i]->option_values;
                        $descripcion_id = $descripcion_id.' / '.$arrayData[$i]->option_values_id;
                        for($j = 0; $j<count($arrayData); $j++){
                            if($arrayData[$j]->id == $idval_2 && $arrayData[$i]->id == $idval_2){
                                if($arrayData[$i]->option_values != $arrayData[$j]->option_values){
                                    $validatelast = explode(" / ", $descripcion);
                                    if($validatelast[0] != $arrayData[$j]->option_values){
                                        $object3 = (object) [
                                            'id' => $idval,
                                            'sort' => $sort,
                                            'descripcion' => $descripcion.' / '.$arrayData[$j]->option_values.'<br><small>'.$titlevideo.'</small>',
                                            'descripcion_id' => $descripcion_id.' / '.$arrayData[$j]->option_values_id,
                                            'precio' => $precio,
                                            'img' => $img,
                                            'id_item_stripe' => $id_item_stripe,
                                            'interval_stripe' => $interval_stripe,
                                            'interval_count_stripe' => $interval_count_stripe,
                                        ];
                                        array_push($arrayData3, $object3);
                                    }
                                }
                            }
                            $idval_2 = $arrayData[$j]->id;
                        }
                    }
                    $idval = $arrayData[$i]->id;
                    $sort = $arrayData[$i]->sort;
                    $descripcion = $arrayData[$i]->option_values;
                    $descripcion_id = $arrayData[$i]->option_values_id;
                    $precio = $arrayData[$i]->precio;
                    $img = $arrayData[$i]->img;
                    $titlevideo = $arrayData[$i]->titlevideo;
                    $id_item_stripe = $arrayData[$i]->id_item_stripe;
                    $interval_stripe = $arrayData[$i]->interval_stripe;
                    $interval_count_stripe = $arrayData[$i]->interval_count_stripe;
                }
/*
                return response()->json([
                    203 => 'Non-Authoritative Information', 
                    'message' => 'Working..',
                  ], 203);*/
            }
            
            // lista simple_2
            $arrayData5 = array();
            for($i = 0; $i<count($arrayData); $i++){
                array_push($arrayData5, $arrayData[$i]->option_keys_id);
            }
            $lista_simple2 = array_values(array_unique($arrayData5));
            $object2 = (object) [
                'idvideo' => $idvideo,
                'id_product_stripe' => $id_product_stripe,
                'options_keys' => $lista_simple,
                'options_keys_id' => $lista_simple2,
                'options_values' => $arrayData3,
            ];
            array_push($arrayData2, $object2);
            return response()->json($arrayData2); 

        }else{
            $status = (object) [
                'status' => false
            ];
            return response()->json($status); 
        }
    }
    
    public function show2(Request $request){
        $request->validate([
            'idvideo' => 'required',
        ]);
        $idvideo = $request->idvideo;
        $results = DB::select( DB::raw('SELECT options.id, options.idvideo, options.descripcion as options_keys, optionvalue.descripcion as options_values, optionvaluemix.precio  FROM optionvaluemix INNER JOIN optionvalue ON optionvalue.id = optionvaluemix.idoptionvalue INNER JOIN options ON options.id = optionvalue.idoption WHERE options.idvideo = "'.$idvideo.'"') );

        if($results){
            return response()->json($results);   
        }else{
            return response()->json(false);
        }
    }
}
