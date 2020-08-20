<?php
namespace App\Http\Controllers;
use App\Transaccion;
use Illuminate\Http\Request;
use App\Http\Requests\TransaccionRequest;
use Illuminate\Support\Facades\DB;
class TransaccionController extends Controller{
    public function show(Request $request){
        $request->validate([
            'id_firebase' => 'required',
            'page' => 'required',
            'items' => 'required',
            'idioma' => 'required',
        ]);
        $id_firebase = $request->id_firebase;
        $page = $request->page;
        $items = $request->items;
        $idioma = $request->idioma;
        $busqueda = $request->busqueda;
        
        $results = DB::select( DB::raw('SELECT ordens.id as ordenid, ordens.numero as ordentitle, ordens.fechaentrega, ordens.descripcion as ordendescripcion, ordens.total, usuarios.name as comprador, usuarios.photo as thumbImg, max(ordenestados.descripcion) as Estados,(SELECT traduccions.descripcion FROM ordenestados INNER JOIN valuesidiomas ON ordenestados.validiomaId = valuesidiomas.id INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON traduccions.idiomaId = idiomas.id WHERE idiomas.id = "'.$idioma.'" and ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS traduccion FROM transaccions INNER JOIN ordens ON ordens.id = transaccions.ordenId INNER JOIN usuarios ON usuarios.id = transaccions.userIdvendedor INNER JOIN ordencambiaestados ON ordencambiaestados.ordenId = transaccions.ordenId INNER JOIN ordenestados ON ordenestados.id = ordencambiaestados.ordenestadoId WHERE usuarios.id_firebase = "'.$id_firebase.'" GROUP BY ordenId ORDER BY ordens.fechaentrega DESC') );
        if($results){
            return response()->json($results);   
        }else{
            return response()->json(false);
        }

    }
}
