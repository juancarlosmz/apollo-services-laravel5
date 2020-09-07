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

            $exchange = round( bcdiv($exchange,'1','3'), 2, PHP_ROUND_HALF_EVEN);
              
            return response()->json([
                'iso_currency' => $iso,
                'exchange' => $exchange,
              ], 200);
 
        }else{
            return response()->json([
                'status' => 401,
                'message' => 'No data',
              ], 401);
        }
    }
}

