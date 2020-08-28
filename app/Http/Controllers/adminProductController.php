<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class adminProductController extends Controller{
    public function optionvaluemixall_admin(Request $request){
        $request->validate([
            'idvideo' => 'required',
            'model' => 'required',
        ]);
        $idvideo = $request->idvideo;
        $model = $request->model;  

        $results = DB::select( DB::raw('select videos.idpmtype,videos.titlevideo,optionvaluemix.idvideo as idvideo, optionvaluemix.id,options.id as option_keys_id, options.descripcion as option_keys,optionvalue.id as option_values_id, optionvalue.descripcion as option_values, optionvaluemix.precio, optionvaluemix.img, optionvaluemix.sort,optionvaluemix.id_item_stripe,optionvaluemix.interval_stripe,optionvaluemix.interval_count_stripe,videos.id_product_stripe from optionvaluemix INNER JOIN options ON options.id = optionvaluemix.idoption INNER JOIN optionvalue ON optionvalue.id = optionvaluemix.idoptionvalue INNER JOIN videos ON videos.id = optionvaluemix.idvideo where optionvaluemix.idvideo = "'.$idvideo.'" ORDER by optionvaluemix.sort ASC, options.descripcion ASC'));

        $results2 = DB::select( DB::raw('SELECT videos.titlevideo, videos.VideoDescription,videos.urlimagen,videos.public,videos.idpmtype from videos WHERE videos.id = "'.$idvideo.'";'));

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

            if($model == 0){
                $idval=0;
                $descripcion = '';
                $descripcion_id = '';
                $precio = 0;
                $img = '';
                $titlevideo = '';
                $arrayData3 = array();
                // primera opcion
                for($i = 0; $i<count($arrayData); $i++){
                    if($arrayData[$i]->id == $idval){
                        $descripcion = $descripcion.' / '.$arrayData[$i]->option_values;
                        $descripcion_id = $descripcion_id.' / '.$arrayData[$i]->option_values_id;
                        $object3 = (object) [
                            'id' => $idval,
                            'descripcion' => $descripcion,
                            'descripcion_id' => $descripcion_id,
                            'precio' => $precio,
                            'img' => $img,
                            'id_item_stripe' => $id_item_stripe,
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
                }
                if(count($arrayData3) == 0){
                    for($i = 0; $i<count($arrayData); $i++){
                        $object3 = (object) [
                            'id' => $arrayData[$i]->id,
                            'descripcion' => $arrayData[$i]->option_values,
                            'descripcion_id' => $arrayData[$i]->option_values_id,
                            'precio' => $arrayData[$i]->precio,
                            'img' => $arrayData[$i]->img,
                            'id_item_stripe' => $arrayData[$i]->id_item_stripe,
                        ];
                        array_push($arrayData3, $object3);
                    }
                }
                // lista simple_1
                $arrayData4 = array();
                for($i = 0; $i<count($arrayData); $i++){
                    array_push($arrayData4, $arrayData[$i]->option_keys);
                }
                $lista_simple = array_values(array_unique($arrayData4));
                // lista simple_2
                $arrayData5 = array();
                for($i = 0; $i<count($arrayData); $i++){
                    array_push($arrayData5, $arrayData[$i]->option_keys_id);
                }
                $lista_simple2 = array_values(array_unique($arrayData5));
                $object2 = (object) [
                    'videoprofile' => $results2,
                    'idvideo' => $idvideo,
                    'id_product_stripe' => $id_product_stripe,
                    'options_keys' => $lista_simple,
                    'options_keys_id' => $lista_simple2,
                    'options_values' => $arrayData3,
                ];
                array_push($arrayData2, $object2);
                return response()->json($arrayData2); 
            }else if($model == 1){
                $arraymodelo1_1 = array();
                $arraymodelo1_2 = array();

                for($i = 0; $i<count($arrayData); $i++){
                    array_push($arraymodelo1_1, $arrayData[$i]->option_keys.'@#@'.$arrayData[$i]->option_keys_id);
                    array_push($arraymodelo1_2, $arrayData[$i]->option_values.'@#@'.$arrayData[$i]->option_keys_id);
                }
                $resultadomodelo1_1 = array_unique($arraymodelo1_1);
                $resultadomodelo1_2 = array_unique($arraymodelo1_2);
                //return response()->json($resultadomodelo1_1); 
                $ultimoresultado = array();

                for($i = 0; $i<count($resultadomodelo1_1); $i++){
                    $mymodels1[$i] = explode("@#@", $resultadomodelo1_1[$i]);
                    $optionss = '';
                    for($z = 0; $z<count(array_values($resultadomodelo1_2)); $z++){
                        
                        $mymodels2[$z] = explode("@#@", array_values($resultadomodelo1_2)[$z]);

                        if($mymodels1[$i][1] == $mymodels2[$z][1]){
                            if($optionss != ''){
                                $optionss = $mymodels2[$z][0].','.$optionss;
                            }else{
                                $optionss = $mymodels2[$z][0];
                            }
                        }  
                    }
                    $model1 = (object) [
                        'optionkey' => $mymodels1[$i][0],
                        'optionval' => $optionss,
                    ];
                    array_push($ultimoresultado, $model1);
                }
                $ultimoresultadoficial = array();
                $model1last = (object) [
                    'videoid' => $idvideo,
                    'payment_type' => $arrayData[0]->idpmtype,
                    'option' => $ultimoresultado,
                ];
                array_push($ultimoresultadoficial, $model1last);

                return response()->json($ultimoresultadoficial[0]); 
            }else if($model == 2){
                $ultimoresultado = array();
                for($i = 0; $i<count($arrayData); $i++){
                    $model2_1 = (object) [
                        'price' => $arrayData[$i]->precio,
                        'interval' => $arrayData[$i]->interval_stripe,
                        'interval_count' => $arrayData[$i]->interval_count_stripe,
                        'option_description' => $arrayData[$i]->option_values,
                        'id_item_stripe' => $arrayData[$i]->id_item_stripe,
                        'sort' => $arrayData[$i]->sort,
                    ];
                    array_push($ultimoresultado, $model2_1);
                }
                $ultimoresultadoficial = array();
                $model2_2 = (object) [
                    'videoid' => $idvideo,
                    'payment_type' => $arrayData[0]->idpmtype,
                    'option' => $arrayData[0]->option_keys,
                    'optionvalues' => $ultimoresultado,
                ];
                array_push($ultimoresultadoficial, $model2_2);
                return response()->json($ultimoresultadoficial[0]); 
            }else if($model == 3){
                $ultimoresultado = array();
                for($i = 0; $i<count($arrayData); $i++){
                    $model2_1 = (object) [
                        'price' => $arrayData[$i]->precio,
                        'option_description' => $arrayData[$i]->option_values,
                        'sort' => $arrayData[$i]->sort,
                    ];
                    array_push($ultimoresultado, $model2_1);
                }
                $ultimoresultadoficial = array();
                $model2_2 = (object) [
                    'videoid' => $idvideo,
                    'payment_type' => $arrayData[0]->idpmtype,
                    'option' => $arrayData[0]->option_keys,
                    'optionvalues' => $ultimoresultado,
                ];
                array_push($ultimoresultadoficial, $model2_2);
                return response()->json($ultimoresultadoficial[0]); 
            }
        }else{
            $status = (object) [
                'videoprofile' => $results2,
                'status' => false
            ];
            return response()->json($status); 
        }
    }
}