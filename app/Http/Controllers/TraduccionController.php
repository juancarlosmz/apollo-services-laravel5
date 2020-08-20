<?php
namespace App\Http\Controllers;
use App\Traduccion;
use Illuminate\Http\Request;
use App\Http\Requests\TraduccionRequest;
use Illuminate\Support\Facades\DB;
class TraduccionController extends Controller{
    public function show($idioma){
        $data = DB::table('traduccions')
       ->join('idiomas', 'traduccions.idiomaId', '=', 'idiomas.id')
       ->join('valuesidiomas', 'traduccions.validiomaId', '=', 'valuesidiomas.id')
       ->select('valuesidiomas.descripcion as value', 'traduccions.descripcion as traduccion', 'idiomas.descripcion as idioma')
       ->where('idiomas.id', $idioma)
       ->get();
        return $data;
    }
}
