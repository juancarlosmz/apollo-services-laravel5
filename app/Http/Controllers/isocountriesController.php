<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class isocountriesController extends Controller{
    public function isocountries($iso_country){
        $client = new Client();
        $response = $client->request('GET', 'https://data.fixer.io/api/latest?access_key=cc2ec08bde4d4561b856971f873140f7&base=USD');
        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents());

        $results = DB::select( DB::raw('SELECT iso_currency FROM isocountries WHERE iso_country = "'.$iso_country.'"'));

        if($results){
            $iso = $results[0]->iso_currency;
            $exchange = $body->rates->$iso;

            //$exchange = round(bcdiv($exchange,'1','3'), 2, PHP_ROUND_HALF_EVEN);
            $arrayData = array();
              
            return response()->json([
                'iso_currency' => $iso,
                'exchange' => $exchange,
                'base' => $body->base,
                'rates' => $body->rates,
              ], 200);
 
        }else{
            return response()->json([
                'status' => 401,
                'message' => 'No data',
              ], 401);
        }
    }
    public function currencylist($iso_country, $firebasebusiness){

        $shearchbusiness = DB::select( DB::raw('SELECT businesses.id,business_isocurrency.iso_currency,business_isocurrency.iso_country FROM businesses INNER JOIN usuarios ON usuarios.id = businesses.userId INNER JOIN business_isocurrency ON business_isocurrency.idbusiness = businesses.id WHERE usuarios.id_firebase = "'.$firebasebusiness.'"')); 

        $results = DB::select( DB::raw('SELECT isocountries.country, isocountries.iso_currency, isocountries.iso_country FROM isocountries ORDER BY isocountries.iso_country = "'.$iso_country.'" DESC, isocountries.country ASC;'));
        //return response()->json($shearchbusiness, 200);
        if($shearchbusiness){
            if($results){
                $arrayData = array();
                for($i = 0; $i<count($results); $i++){
                    $selected = false;
                    if($results[$i]->iso_country == $shearchbusiness[0]->iso_country){
                        $selected = true;
                    }
                    $object = (object) [
                        'country' => $results[$i]->country,
                        'iso_currency' => $results[$i]->iso_currency,
                        'iso_country' => $results[$i]->iso_country,
                        'selected' => $selected,
                    ];
                    array_push($arrayData, $object);
                }
                return response()->json($arrayData, 200);
            }else{
                return response()->json([
                    'result' => 'No data',
                  ], 401);
            }
        }else{
            if($results){
                $arrayData = array();
                for($i = 0; $i<count($results); $i++){
                    
                    $selected = false;
                    /*
                    if($results[$i]->iso_country == $iso_country){
                        $selected = true;
                    }*/
                    $object = (object) [
                        'country' => $results[$i]->country,
                        'iso_currency' => $results[$i]->iso_currency,
                        'iso_country' => $results[$i]->iso_country,
                        'selected' => $selected,
                    ];
                    array_push($arrayData, $object);
                }
                return response()->json($arrayData, 200);
            }
        }

        
    }

    public function businesscurrency(Request $request){

        $request->validate([
            'id_firebase_vendedor' => 'required',
            'iso_currency' => 'required',
            'iso_country' => 'required',
        ]);
        $id_firebase_vendedor = $request->id_firebase_vendedor;
        $iso_currency = $request->iso_currency;
        $iso_country = $request->iso_country;

        $businessesID = DB::select( DB::raw('SELECT businesses.id FROM usuarios INNER JOIN businesses ON businesses.userId = usuarios.id WHERE usuarios.id_firebase = "'.$id_firebase_vendedor.'";'));
        if($businessesID){

            $validatebusiness_isocurrency = DB::select( DB::raw('SELECT id FROM business_isocurrency WHERE business_isocurrency.idbusiness = "'.$businessesID[0]->id.'";'));

            if($validatebusiness_isocurrency){
                $updateisocountries = DB::select( DB::raw('UPDATE business_isocurrency SET idbusiness = "'.$businessesID[0]->id.'", iso_currency = "'.$iso_currency.'", iso_country = "'.$iso_country.'", updated_at = now() WHERE business_isocurrency.idbusiness = "'.$businessesID[0]->id.'";') );
            }else{
                $instertisocountries = DB::select( DB::raw('INSERT INTO business_isocurrency (id, idbusiness, iso_currency,iso_country,created_at, updated_at) VALUES (NULL, "'.$businessesID[0]->id.'","'.$iso_currency.'","'.$iso_country.'",now(), now());') );
            }
            return response()->json([
                200 => 'OK',
              ], 200);
        }else{
            return response()->json([
                401 => 'Unauthorized',
              ], 401);
        }
    }
}

