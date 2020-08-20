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
        $results = DB::select( DB::raw('select videos.titlevideo,optionvaluemix.idvideo as idvideo, optionvaluemix.id, options.descripcion as option_keys, optionvalue.descripcion as option_values, optionvaluemix.precio, optionvaluemix.sort from optionvaluemix INNER JOIN options ON options.id = optionvaluemix.idoption INNER JOIN optionvalue ON optionvalue.id = optionvaluemix.idoptionvalue INNER JOIN videos ON videos.id = optionvaluemix.idvideo where optionvaluemix.idvideo = "'.$idvideo.'" ORDER by optionvaluemix.sort ASC, options.descripcion ASC'));

        if($results){
            $arrayData = array();
            $arrayData2 = array();
            $idvideo;
            $mytitlevideo = '';
            for($i = 0; $i<count($results); $i++){
                $idvideo = $results[$i]->idvideo;
                $mytitlevideo = strip_tags($results[$i]->titlevideo);
                $object = (object) [
                    'id' => $results[$i]->id,
                    'option_keys' => $results[$i]->option_keys,
                    'option_values' => $results[$i]->option_values,
                    'precio' => $results[$i]->precio,
                    'titlevideo' => str_replace('<br>',' ',$results[$i]->titlevideo),
                ];
                array_push($arrayData, $object);
            }
            $idval=0;
            $descripcion = '';
            $precio = 0;
            $titlevideo = '';
            $arrayData3 = array();
            // primera opcion

            for($i = 0; $i<count($arrayData); $i++){
                if($arrayData[$i]->id == $idval){
                    $descripcion = $descripcion.' / '.$arrayData[$i]->option_values.'<br><small>'.$titlevideo.'</small>';
                    $object3 = (object) [
                        'id' => $idval,
                        'descripcion' => $descripcion,
                        'precio' => $precio,
                    ];
                    array_push($arrayData3, $object3);
                }
                $idval = $arrayData[$i]->id;
                $descripcion = $arrayData[$i]->option_values;
                $precio = $arrayData[$i]->precio;
                $titlevideo = $arrayData[$i]->titlevideo;
            }
            if(count($arrayData3) == 0){
                for($i = 0; $i<count($arrayData); $i++){
                    $object3 = (object) [
                        'id' => $arrayData[$i]->id,
                        'descripcion' => $arrayData[$i]->option_values.'<br><small>'.$arrayData[$i]->titlevideo.'</small>',
                        'precio' => $arrayData[$i]->precio,
                    ];
                    array_push($arrayData3, $object3);
                }
            }
            
            //
            $arrayData4 = array();
            for($i = 0; $i<count($arrayData); $i++){
                array_push($arrayData4, $arrayData[$i]->option_keys);
            }
            $lista_simple = array_values(array_unique($arrayData4));
            $object2 = (object) [
                'idvideo' => $idvideo,
                'options_keys' => $lista_simple,
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
