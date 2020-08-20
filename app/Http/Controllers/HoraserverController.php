<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoraserverController extends Controller
{
    public function show(){
        $horaserver = (object) [
            'horaserver' => date_default_timezone_get(),
            'REQUEST_TIME' => $_SERVER['REQUEST_TIME']
        ];
        /*
        $zones_array = array();        
        $timestamp = time();         
        # to maintain the original timezone, store before
        $default_timezone = date_default_timezone_get();
        foreach (timezone_identifiers_list() as $key => $zone) {    
            date_default_timezone_set($zone);
            $zones_array[$key]['zone'] = $zone;
            $zones_array[$key]['diff_from_GMT'] = date('P', $timestamp);
        }
        # to maintain the original timezone, re-set after
        date_default_timezone_set($default_timezone);    
        print_r($zones_array);
        */

        /*
        $date = date_create();
        $dateStamp = $_SERVER['REQUEST_TIME'];
        date_timestamp_set($date, $dateStamp);
        echo date_format($date, 'U = D-M-Y H:i:s') . "\n";
        */

        $timestamp = time()+date("Z");
        $horaserver = (object) [
            'SERVER_TIME' => gmdate("Y/m/d H:i:s",$timestamp),
            'GMT' => date('P', $timestamp),
        ]; 
        return response()->json($horaserver);   
    }
}
