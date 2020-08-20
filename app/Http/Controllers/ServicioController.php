<?php
namespace App\Http\Controllers;
use App\Servicio;
use Illuminate\Http\Request;
use App\Http\Requests\ServicioRequest;
use Illuminate\Support\Facades\DB;
class ServicioController extends Controller{
    public function show($idioma){
        $data = DB::table('servicios')
            ->join('valuesidiomas', 'servicios.validiomaId', '=', 'valuesidiomas.id')
            ->join('traduccions', 'traduccions.validiomaId', '=', 'valuesidiomas.id')
            ->join('idiomas', 'traduccions.idiomaId', '=', 'idiomas.id')
            ->select('servicios.id as id', 'traduccions.descripcion as traduccion')
            ->where('idiomas.id', $idioma)
            ->get();
        return $data;
    }
    public function show2($idioma){
        $results2 = DB::select( DB::raw('select servicios.id as id, traduccions.descripcion as traduccion FROM servicios INNER JOIN valuesidiomas ON servicios.validiomaId = valuesidiomas.id INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$idioma.'" and servicios.nvideos > 0;') ); 
        return response()->json($results2); 
    }
}
