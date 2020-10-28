<?php
namespace App\Http\Controllers;
use App\Ordenestado;
use Illuminate\Http\Request;
use App\Http\Requests\OrdenestadoRequest;
use Illuminate\Support\Facades\DB;
// Firebase conect
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
class OrdenestadoController extends Controller{
    public function show($id_firebase, $idioma){
        // variables
        $userIdcomprador;
        $data = DB::table('ordenestados')
            ->join('ordencambiaestados', 'ordenestados.id', '=', 'ordencambiaestados.ordenestadoId')
            ->join('ordens', 'ordens.id', '=', 'ordencambiaestados.ordenId')
            ->join('transaccions', 'transaccions.ordenId', '=', 'ordens.id')
            ->join('usuarios', 'usuarios.id', '=', 'transaccions.userIdvendedor')
            ->select('ordens.id', 'ordens.numero', 'ordens.fechaentrega', 'ordens.descripcion as ordendescripcion', 'ordens.total','transaccions.userIdcomprador', DB::raw('max(ordenestados.descripcion) as Estados'), DB::raw('(SELECT traduccions.descripcion FROM ordenestados INNER JOIN valuesidiomas ON ordenestados.validiomaId = valuesidiomas.id INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON traduccions.idiomaId = idiomas.id WHERE idiomas.id = "'.$idioma.'" and ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS traduccion'))
            ->where('usuarios.id_firebase', $id_firebase)
            ->groupBy('ordens.id')
            ->havingRaw("COUNT(ordencambiaestados.ordenestadoId) = 2")
            ->orderBy('ordens.fechaentrega', 'DESC')
            ->get();                 
        if($data){
            $arrayIds = array();
            foreach ($data as $key => $dt) {
                $dataUser = DB::table('usuarios')
                    ->select('usuarios.name as comprador', 'usuarios.photo as thumbImg')
                    ->where('usuarios.id', $dt->userIdcomprador)
                    ->get();
                array_push($arrayIds, $dataUser);    
            }
            $arrayData = array();
            for($i = 0; $i<count($arrayIds); $i++){
                $object = (object) [
                    'ordenid' => $data[$i]->id,
                    'ordentitle' => $data[$i]->numero,
                    'fechaentrega' => $data[$i]->fechaentrega,
                    'ordendescripcion' => $data[$i]->ordendescripcion,
                    'total' => $data[$i]->total,
                    'comprador' => $arrayIds[$i][0]->comprador,
                    'thumbImg' => $arrayIds[$i][0]->thumbImg,
                    'estado' => $data[$i]->Estados,
                    'traduccion' => $data[$i]->traduccion
                ];
                array_push($arrayData, $object);
            }
            return response()->json($arrayData); 
        } else {
            return false;
        }       
    }
    public function show_1(Request $request){
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
        //paginacion
        $pageskip = intval($page) - 1;
        $items = intval($items);
        $data = DB::table('ordenestados')
            ->join('ordencambiaestados', 'ordenestados.id', '=', 'ordencambiaestados.ordenestadoId')
            ->join('ordens', 'ordens.id', '=', 'ordencambiaestados.ordenId')
            ->join('transaccions', 'transaccions.ordenId', '=', 'ordens.id')
            ->join('usuarios', 'usuarios.id', '=', 'transaccions.userIdvendedor')
            ->join('pagos', 'pagos.ordenId', '=', 'ordens.id')
            ->select('pagos.cliente_isocurrency','pagos.cliente_tc','pagos.business_isocurrency','pagos.business_tc','ordens.id', 'ordens.numero', 'ordens.fechaentrega', 'ordens.descripcion as ordendescripcion', 'ordens.total','transaccions.userIdcomprador', DB::raw('max(ordenestados.descripcion) as Estados'), DB::raw('(SELECT traduccions.descripcion FROM ordenestados INNER JOIN valuesidiomas ON ordenestados.validiomaId = valuesidiomas.id INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON traduccions.idiomaId = idiomas.id WHERE idiomas.id = "'.$idioma.'" and ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS traduccion'))
            ->where('usuarios.id_firebase', $id_firebase)
            ->groupBy('ordens.id')
            ->havingRaw("COUNT(ordencambiaestados.ordenestadoId) = 2")
            ->orderBy('ordens.fechaentrega', 'DESC')
            ->limit($items + 1)
            ->offset(($pageskip) * $items)
            ->get();    
        //   
        $nextpage = $page+1;
        // next - previous
        $next = '';
        if($data->count() > $items){
            $next = 'https://apolomultimedia-server4.info/ListadodeOrdenesPendientesPaginado/?id_firebase='.$id_firebase.'&page='.$nextpage.'&items='.$items.'&idioma='.$idioma;
        }
        unset($data[$items]);    
        if($data){
            $arrayIds = array();
            foreach ($data as $key => $dt) {
                $dataUser = DB::table('usuarios')
                    ->select('usuarios.name as comprador', 'usuarios.photo as thumbImg')
                    ->where('usuarios.id', $dt->userIdcomprador)
                    ->get();
                array_push($arrayIds, $dataUser);   
            }
            $arrayData = array();
            $arrayDataandcount = array();
            //
            for($i = 0; $i<count($arrayIds); $i++){
                $cliente_isocurrency = 'PEN';
                if($data[$i]->cliente_isocurrency != ""){
                    $cliente_isocurrency = $data[$i]->cliente_isocurrency;
                }

                $cliente_tc = 3.583501;
                if($data[$i]->cliente_tc != 0){
                    $cliente_tc = $data[$i]->cliente_tc;
                }

                $business_isocurrency = 'PEN';
                if($data[$i]->business_isocurrency != ""){
                    $business_isocurrency = $data[$i]->business_isocurrency;
                }

                $business_tc = 1;
                if($data[$i]->business_tc != 0){
                    $business_tc = $data[$i]->business_tc;
                }
                $object = (object) [
                    'ordenid' => $data[$i]->id,
                    'ordentitle' => $data[$i]->numero,
                    'fechaentrega' => $data[$i]->fechaentrega,
                    'ordendescripcion' => $data[$i]->ordendescripcion,
                    'total' => $data[$i]->total,
                    'comprador' => $arrayIds[$i][0]->comprador,
                    'thumbImg' => $arrayIds[$i][0]->thumbImg,
                    'estado' => $data[$i]->Estados,
                    'traduccion' => $data[$i]->traduccion,
                    'cliente_isocurrency' => $cliente_isocurrency,
                    'cliente_tc' => $cliente_tc,
                    'business_isocurrency' => $business_isocurrency,
                    'business_tc' => $business_tc,
                ];
                array_push($arrayData, $object);
            }
            $lastobject = (object) [
                'page' => $page,
                'next' => $next,
                'results' => $arrayData,
            ];
            array_push($arrayDataandcount,$lastobject);
            return response()->json($arrayDataandcount); 
        } else {
            return false;
        }               
    }
    public function show2($id_firebase, $idioma){
        // variables
        $data = DB::table('ordenestados')
            ->join('ordencambiaestados', 'ordenestados.id', '=', 'ordencambiaestados.ordenestadoId')
            ->join('ordens', 'ordens.id', '=', 'ordencambiaestados.ordenId')
            ->join('transaccions', 'transaccions.ordenId', '=', 'ordens.id')
            ->join('usuarios', 'usuarios.id', '=', 'transaccions.userIdvendedor')
            ->join('pagos', 'pagos.ordenId', '=', 'ordens.id')
            ->select('ordens.id', 'ordens.numero', 'ordens.fechaentrega', 'ordens.descripcion as ordendescripcion', 'ordens.total','transaccions.userIdcomprador', DB::raw('(SELECT ordenestados.descripcion FROM ordenestados WHERE ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS descripcion'), DB::raw('(SELECT traduccions.descripcion FROM ordenestados INNER JOIN valuesidiomas ON ordenestados.validiomaId = valuesidiomas.id INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON traduccions.idiomaId = idiomas.id WHERE idiomas.id = "'.$idioma.'" and ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS traduccion'))
            ->where('usuarios.id_firebase', $id_firebase)
            ->groupBy('ordens.id')
            ->orderBy('ordens.fechaentrega', 'DESC')
            ->get();               
        if($data){
            $arrayIds = array();
            foreach ($data as $key => $dt) {
                $dataUser = DB::table('usuarios')
                    ->select('usuarios.name as comprador', 'usuarios.photo as thumbImg')
                    ->where('usuarios.id', $dt->userIdcomprador)
                    ->get();
                array_push($arrayIds, $dataUser);      
            }
            $arrayData = array();
            for($i = 0; $i<count($arrayIds); $i++){
                $object = (object) [
                    'ordenid' => $data[$i]->id,
                    'ordentitle' => $data[$i]->numero,
                    'fechaentrega' => $data[$i]->fechaentrega,
                    'ordendescripcion' => $data[$i]->ordendescripcion,
                    'total' => $data[$i]->total,
                    'comprador' => $arrayIds[$i][0]->comprador,
                    'thumbImg' => $arrayIds[$i][0]->thumbImg,
                    'estado' => $data[$i]->descripcion,
                    'traduccion' => $data[$i]->traduccion
                ];
                array_push($arrayData, $object);
            }
            return response()->json($arrayData); 
        } else {
            return false;
        }         
    }
    
    public function show2_1(Request $request){
        $request->validate([
            'id_firebase' => 'required',
            'page' => 'required',
            'items' => 'required',
            'idioma' => 'required',
            'sort' => 'required',
        ]);
        $id_firebase = $request->id_firebase;
        $page = $request->page;
        $items = $request->items;
        $idioma = $request->idioma;
        $sort = $request->sort;
        //paginacion
        $pageskip = intval($page) - 1;
        $items = intval($items);
        //sort ordenamiento 
        $sortMysql = '';
        if($sort == 'fecha'){
            $sortMysql = 'ordens.fechaentrega';
        }else if($sort == 'id'){
            $sortMysql = 'ordens.id';
        }
        $data = DB::table('ordenestados')
            ->join('ordencambiaestados', 'ordenestados.id', '=', 'ordencambiaestados.ordenestadoId')
            ->join('ordens', 'ordens.id', '=', 'ordencambiaestados.ordenId')
            ->join('transaccions', 'transaccions.ordenId', '=', 'ordens.id')
            ->join('usuarios', 'usuarios.id', '=', 'transaccions.userIdvendedor')
            ->join('pagos', 'pagos.ordenId', '=', 'ordens.id')
            ->select('pagos.cliente_isocurrency','pagos.cliente_tc','pagos.business_isocurrency','pagos.business_tc','ordens.id', 'ordens.numero', 'ordens.fechaentrega', 'ordens.descripcion as ordendescripcion', 'ordens.total','transaccions.userIdcomprador', DB::raw('(SELECT ordenestados.descripcion FROM ordenestados WHERE ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS descripcion'), DB::raw('(SELECT traduccions.descripcion FROM ordenestados INNER JOIN valuesidiomas ON ordenestados.validiomaId = valuesidiomas.id INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON traduccions.idiomaId = idiomas.id WHERE idiomas.id = "'.$idioma.'" and ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS traduccion'))
            ->where('usuarios.id_firebase', $id_firebase)
            ->groupBy('ordens.id')
            ->orderBy($sortMysql, 'DESC')
            ->limit($items + 1 )
            ->offset(($pageskip) * $items)
            ->get();
        //   
        $nextpage = $page+1;
        // next - previous
        $next = '';
        if($data->count() > $items){
            $next = 'https://apolomultimedia-server4.info/ListadodeOrdenesPaginado/?id_firebase='.$id_firebase.'&page='.$nextpage.'&items='.$items.'&idioma='.$idioma.'&sort='.$sort;
        }
        unset($data[$items]);
        if($data){
            $arrayIds = array();
            foreach ($data as $key => $dt) {
                $dataUser = DB::table('usuarios')
                    ->select('usuarios.name as comprador', 'usuarios.photo as thumbImg')
                    ->where('usuarios.id', $dt->userIdcomprador)
                    ->get();
                array_push($arrayIds, $dataUser);    
            }
            $arrayData = array();
            $arrayDataandcount = array();
            for($i = 0; $i<count($arrayIds); $i++){

                $cliente_isocurrency = 'PEN';
                if($data[$i]->cliente_isocurrency != ""){
                    $cliente_isocurrency = $data[$i]->cliente_isocurrency;
                }

                $cliente_tc = 3.583501;
                if($data[$i]->cliente_tc != 0){
                    $cliente_tc = $data[$i]->cliente_tc;
                }

                $business_isocurrency = 'PEN';
                if($data[$i]->business_isocurrency != ""){
                    $business_isocurrency = $data[$i]->business_isocurrency;
                }

                $business_tc = 1;
                if($data[$i]->business_tc != 0){
                    $business_tc = $data[$i]->business_tc;
                }

                $object = (object) [
                    'ordenid' => $data[$i]->id,
                    'ordentitle' => $data[$i]->numero,
                    'fechaentrega' => $data[$i]->fechaentrega,
                    'ordendescripcion' => $data[$i]->ordendescripcion,
                    'total' => $data[$i]->total,
                    'comprador' => $arrayIds[$i][0]->comprador,
                    'thumbImg' => $arrayIds[$i][0]->thumbImg,
                    'estado' => $data[$i]->descripcion,
                    'traduccion' => $data[$i]->traduccion,
                    'cliente_isocurrency' => $cliente_isocurrency,
                    'cliente_tc' => $cliente_tc,
                    'business_isocurrency' => $business_isocurrency,
                    'business_tc' => $business_tc,
                ];
                array_push($arrayData, $object);
            }
            $lastobject = (object) [
                'page' => $page,
                'next' => $next,
                'results' => $arrayData,
            ];
            array_push($arrayDataandcount,$lastobject);
            return response()->json($arrayDataandcount); 
        } else {
            return false;
        }         
    }
    public function show2_2(Request $request){
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
        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        // variables
        $userIdcomprador;
        $data = DB::table('ordenestados')
            ->join('ordencambiaestados', 'ordenestados.id', '=', 'ordencambiaestados.ordenestadoId')
            ->join('ordens', 'ordens.id', '=', 'ordencambiaestados.ordenId')
            ->join('transaccions', 'transaccions.ordenId', '=', 'ordens.id')
            ->join('usuarios', 'usuarios.id', '=', 'transaccions.userIdvendedor')
            ->join('pagos', 'pagos.ordenId', '=', 'ordens.id')
            ->select('pagos.cliente_isocurrency','pagos.cliente_tc','pagos.business_isocurrency','pagos.business_tc','ordens.id', 'ordens.numero', 'ordens.fechaentrega', 'ordens.descripcion as ordendescripcion', 'ordens.total','transaccions.userIdcomprador', DB::raw('(SELECT ordenestados.descripcion FROM ordenestados WHERE ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS descripcion'), DB::raw('(SELECT traduccions.descripcion FROM ordenestados INNER JOIN valuesidiomas ON ordenestados.validiomaId = valuesidiomas.id INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON traduccions.idiomaId = idiomas.id WHERE idiomas.id = "'.$idioma.'" and ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS traduccion'))
            ->where('usuarios.id_firebase', $id_firebase)
            ->whereRaw('concat(ordens.numero," ",(SELECT usuarios.name FROM usuarios WHERE usuarios.id  = transaccions.userIdcomprador)) like ?', '%'.$busqueda.'%')
            ->groupBy('ordens.id')
            ->orderBy('ordens.fechaentrega', 'DESC')
            ->get();
        //       
        if($data){
            $arrayIds = array();
            foreach ($data as $key => $dt) {
                $dataUser = DB::table('usuarios')
                    //->join('businesses', 'businesses.userId', '=', 'usuarios.id')
                    //->select('usuarios.name as comprador', 'usuarios.photo as thumbImg', 'businesses.logo')
                    ->select('usuarios.name as comprador', 'usuarios.photo as thumbImg')
                    ->where('usuarios.id', $dt->userIdcomprador)
                    ->get();
                array_push($arrayIds, $dataUser);    
            }
            $arrayData = array();
            $arrayDataandcount = array();
            for($i = 0; $i<count($arrayIds); $i++){
                if($busqueda != ''){

                    $cliente_isocurrency = 'PEN';
                    if($data[$i]->cliente_isocurrency != ""){
                        $cliente_isocurrency = $data[$i]->cliente_isocurrency;
                    }

                    $cliente_tc = 3.583501;
                    if($data[$i]->cliente_tc != 0){
                        $cliente_tc = $data[$i]->cliente_tc;
                    }

                    $business_isocurrency = 'PEN';
                    if($data[$i]->business_isocurrency != ""){
                        $business_isocurrency = $data[$i]->business_isocurrency;
                    }

                    $business_tc = 1;
                    if($data[$i]->business_tc != 0){
                        $business_tc = $data[$i]->business_tc;
                    }

                    $object = (object) [
                        'ordenid' => $data[$i]->id,
                        'ordentitle' => $data[$i]->numero,
                        'fechaentrega' => $data[$i]->fechaentrega,
                        'ordendescripcion' => $data[$i]->ordendescripcion,
                        'total' => $data[$i]->total,
                        'comprador' => $arrayIds[$i][0]->comprador,
                        'thumbImg' => $arrayIds[$i][0]->thumbImg,
                        //'logo' => $arrayIds[$i][0]->logo,
                        'estado' => $data[$i]->descripcion,
                        'traduccion' => $data[$i]->traduccion,
                        'cliente_isocurrency' => $cliente_isocurrency,
                        'cliente_tc' => $cliente_tc,
                        'business_isocurrency' => $business_isocurrency,
                        'business_tc' => $business_tc,
                    ];
                    array_push($arrayData, $object);
                }else{
                    
                }
            }
            //paginacion
            $pageskip = intval($page) - 1;
            $items = intval($items);  
            $nextpage = $page+1;
            // next - previous
            $next = '';
            $arrayDataPaginado = array_slice($arrayData, (($pageskip) * $items), $items);
            if(count($arrayDataPaginado) > ($items-1)){
                $next = 'https://apolomultimedia-server4.info/BuscarOrden/?id_firebase='.$id_firebase.'&page='.$nextpage.'&items='.$items.'&idioma='.$idioma.'&busqueda='.$busqueda;
            }
            $lastobject = (object) [
                'page' => $page,
                'next' => $next,
                'results' => $arrayDataPaginado,
            ];
            array_push($arrayDataandcount,$lastobject);
            return response()->json($arrayDataandcount); 
        } else {
            return false;
        }         
    }
    public function show3($id_firebase,$ordenid){
        // variables
        $object;
        $entregado = '';
        $fechaentregado = '';
        $recibido = '';
        $fecharecibido = '';
        $data = DB::table('ordenestados')
            ->join('ordencambiaestados', 'ordenestados.id', '=', 'ordencambiaestados.ordenestadoId')
            ->join('ordens', 'ordens.id', '=', 'ordencambiaestados.ordenId')
            ->join('transaccions', 'transaccions.ordenId', '=', 'ordens.id')
            ->join('usuarios', 'usuarios.id', '=', 'transaccions.userIdvendedor')
            ->join('pagos', 'pagos.ordenId', '=', 'ordens.id')
            ->join('businesses', 'businesses.userId', '=', 'usuarios.id')
            ->select('pagos.cliente_isocurrency','pagos.cliente_tc','pagos.business_isocurrency','pagos.business_tc','ordens.numero', 'ordens.descripcion as ordendescripcion', 'ordens.fechaentrega', 'ordens.total','businesses.descripcion as bussiness', 'transaccions.userIdcomprador', 'transaccions.userIdvendedor','pagos.fecha','ordenestados.descripcion as estadodescripcion','ordencambiaestados.created_at')
            ->where('usuarios.id_firebase', $id_firebase)
            ->where('ordens.id', $ordenid)
            ->get();
        foreach ($data as $key => $dt) {
            if($dt->estadodescripcion == 'Entregado'){
                $entregado = $dt->estadodescripcion;
                $fechaentregado = $dt->created_at;
            }else if($dt->estadodescripcion == 'Recibido'){
                $recibido = $dt->estadodescripcion;
                $fecharecibido = $dt->created_at;
            }
            $vendedor = DB::table('usuarios')
                    ->select('usuarios.id_firebase as idFirebasevendedor', 'usuarios.name as vendedor')
                    ->where('usuarios.id', $dt->userIdvendedor)
                    ->get();
            $comprador = DB::table('usuarios')
                    ->select('usuarios.id_firebase as idFirebasecliente', 'usuarios.name as comprador')
                    ->where('usuarios.id', $dt->userIdcomprador)
                    ->get(); 
            $cliente_isocurrency = 'PEN';
            if($dt->cliente_isocurrency != ""){
                $cliente_isocurrency = $dt->cliente_isocurrency;
            }

            $cliente_tc = 3.583501;
            if($dt->cliente_tc != 0){
                $cliente_tc = $dt->cliente_tc;
            }

            $business_isocurrency = 'PEN';
            if($dt->business_isocurrency != ""){
                $business_isocurrency = $dt->business_isocurrency;
            }

            $business_tc = 1;
            if($dt->business_tc != 0){
                $business_tc = $dt->business_tc;
            }        
            $object = (object) [
                'ordentitle' => $dt->numero,
                'producto/servicio' => $dt->ordendescripcion,
                'fechaentrega' => $dt->fechaentrega,
                'precio' => $dt->total,
                'idFirebasevendedor' => $vendedor[0]->idFirebasevendedor,
                'negocio/profesional' => $dt->bussiness,
                'idFirebasecliente' => $comprador[0]->idFirebasecliente,
                'cliente' => $comprador[0]->comprador,
                'pago' => $dt->fecha,
                //'marcadoEntregado' => $entregado,
                'fechaentregado' => $fechaentregado,
                //'marcadoRecibido' => $recibido,
                'fecharecibido' => $fecharecibido,
                'cliente_isocurrency' => $cliente_isocurrency,
                'cliente_tc' => $cliente_tc,
                'business_isocurrency' => $business_isocurrency,
                'business_tc' => $business_tc,
            ];           
        }
        if(isset($object)){
            return response()->json($object);
        }else{
            return response()->json(false);
        } 
    }
    public function show4($id_firebase1,$id_firebase2,$idioma){
        $results = DB::select( DB::raw('select transaccions.ordenId, MAX(ordencambiaestados.ordenestadoId) AS Estados FROM `transaccions` INNER JOIN usuarios AS uservendedortable ON uservendedortable.id=transaccions.userIdvendedor AND ( uservendedortable.id_firebase="'.$id_firebase1.'" or uservendedortable.id_firebase="'.$id_firebase2.'") JOIN usuarios AS usercompradortable ON usercompradortable.id=transaccions.userIdcomprador AND (usercompradortable.id_firebase="'.$id_firebase2.'" or usercompradortable.id_firebase="'.$id_firebase1.'") INNER JOIN ordencambiaestados ON transaccions.ordenId=ordencambiaestados.ordenId GROUP by transaccions.ordenId HAVING COUNT(ordencambiaestados.ordenestadoId)=2;') );   
        if($results){
            $data2 = DB::table('traduccions')
                ->join('valuesidiomas', 'traduccions.validiomaId', '=', 'valuesidiomas.id')
                ->join('idiomas', 'traduccions.idiomaId', '=', 'idiomas.id')
                ->select('traduccions.descripcion')
                ->where('idiomas.id', $idioma)
                ->where('valuesidiomas.id', '14')
                ->get();
            return $data2;    
        }else{
            return response()->json(false);
        }
         
    }
    public function show5($id_firebase, $idioma){
        $data = DB::table('ordenestados')
            ->join('ordencambiaestados', 'ordenestados.id', '=', 'ordencambiaestados.ordenestadoId')
            ->join('ordens', 'ordens.id', '=', 'ordencambiaestados.ordenId')
            ->join('transaccions', 'transaccions.ordenId', '=', 'ordens.id')
            ->join('usuarios', 'usuarios.id', '=', 'transaccions.userIdcomprador')
            ->join('pagos', 'pagos.ordenId', '=', 'ordens.id')
            ->join('businesses', 'businesses.userId', '=', 'transaccions.userIdvendedor')
            ->select('ordens.id', 'ordens.numero', 'ordens.fechaentrega', 'ordens.descripcion as ordendescripcion', 'ordens.total','transaccions.userIdvendedor','businesses.descripcion as bussiness', DB::raw('(SELECT ordenestados.descripcion FROM ordenestados WHERE ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS descripcion'), DB::raw('(SELECT traduccions.descripcion FROM ordenestados INNER JOIN valuesidiomas ON ordenestados.validiomaId = valuesidiomas.id INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON traduccions.idiomaId = idiomas.id WHERE idiomas.id = "'.$idioma.'" and ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS traduccion'))
            ->where('usuarios.id_firebase', $id_firebase)
            ->groupBy('ordens.id')
            ->orderBy('ordens.fechaentrega', 'DESC')
            ->get();               
        if($data){
            $arrayIds = array();
            foreach ($data as $key => $dt) {   
                $dataUser = DB::table('usuarios')
                    ->select('usuarios.name as vendedor', 'usuarios.photo as thumbImg')
                    ->where('usuarios.id', $dt->userIdvendedor)
                    ->get();
                array_push($arrayIds, $dataUser); 
            }
            $arrayData = array();
            for($i = 0; $i<count($arrayIds); $i++){
                $object = (object) [
                    'ordenid' => $data[$i]->id,
                    'ordentitle' => $data[$i]->numero,
                    'fechaentrega' => $data[$i]->fechaentrega,
                    'ordendescripcion' => $data[$i]->ordendescripcion,
                    'total' => $data[$i]->total,
                    'negocio/profesional' => $data[$i]->bussiness,
                    'vendedor' => $arrayIds[$i][0]->vendedor,
                    'thumbImg' => $arrayIds[$i][0]->thumbImg,
                    'estado' => $data[$i]->descripcion,
                    'traduccion' => $data[$i]->traduccion
                ];
                array_push($arrayData, $object);
            }
            return response()->json($arrayData); 
        } else {
            return false;
        }         
    }
    public function show5_1(Request $request){
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
        //paginacion
        $pageskip = intval($page) - 1;
        $items = intval($items);
        $data = DB::table('ordenestados')
            ->join('ordencambiaestados', 'ordenestados.id', '=', 'ordencambiaestados.ordenestadoId')
            ->join('ordens', 'ordens.id', '=', 'ordencambiaestados.ordenId')
            ->join('transaccions', 'transaccions.ordenId', '=', 'ordens.id')
            ->join('usuarios', 'usuarios.id', '=', 'transaccions.userIdcomprador')
            ->join('pagos', 'pagos.ordenId', '=', 'ordens.id')
            ->join('businesses', 'businesses.userId', '=', 'transaccions.userIdvendedor')
            ->select('pagos.cliente_isocurrency','pagos.cliente_tc','pagos.business_isocurrency','pagos.business_tc','ordens.id', 'ordens.numero', 'ordens.fechaentrega', 'ordens.descripcion as ordendescripcion', 'ordens.total','transaccions.userIdvendedor','businesses.descripcion as bussiness', DB::raw('(SELECT ordenestados.descripcion FROM ordenestados WHERE ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS descripcion'), DB::raw('(SELECT traduccions.descripcion FROM ordenestados INNER JOIN valuesidiomas ON ordenestados.validiomaId = valuesidiomas.id INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON traduccions.idiomaId = idiomas.id WHERE idiomas.id = "'.$idioma.'" and ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS traduccion'))
            ->where('usuarios.id_firebase', $id_firebase)
            ->groupBy('ordens.id')
            ->orderBy('ordens.fechaentrega', 'DESC')
            ->limit($items + 1)
            ->offset(($pageskip) * $items)
            ->get(); 
        //   
        $nextpage = $page+1;
        // next - previous
        $next = '';
        if($data->count() > $items){
            $next = 'https://apolomultimedia-server4.info/ListadodePedidosPaginado/?id_firebase='.$id_firebase.'&page='.$nextpage.'&items='.$items.'&idioma='.$idioma;
        }
        unset($data[$items]);                  
        if($data){
            $arrayIds = array();
            foreach ($data as $key => $dt) {
                $dataUser = DB::table('usuarios')
                    ->join('businesses', 'businesses.userId', '=', 'usuarios.id')
                    ->select('usuarios.name as vendedor', 'usuarios.photo as thumbImg', 'businesses.logo')
                    ->where('usuarios.id', $dt->userIdvendedor)
                    ->get();
                array_push($arrayIds, $dataUser);   
            }
            $arrayData = array();
            $arrayDataandcount = array();
            for($i = 0; $i<count($arrayIds); $i++){
                $cliente_isocurrency = 'PEN';
                if($data[$i]->cliente_isocurrency != ""){
                    $cliente_isocurrency = $data[$i]->cliente_isocurrency;
                }

                $cliente_tc = 3.583501;
                if($data[$i]->cliente_tc != 0){
                    $cliente_tc = $data[$i]->cliente_tc;
                }

                $business_isocurrency = 'PEN';
                if($data[$i]->business_isocurrency != ""){
                    $business_isocurrency = $data[$i]->business_isocurrency;
                }

                $business_tc = 1;
                if($data[$i]->business_tc != 0){
                    $business_tc = $data[$i]->business_tc;
                }
                $object = (object) [
                    'ordenid' => $data[$i]->id,
                    'ordentitle' => $data[$i]->numero,
                    'fechaentrega' => $data[$i]->fechaentrega,
                    'ordendescripcion' => $data[$i]->ordendescripcion,
                    'total' => $data[$i]->total,
                    'negocio/profesional' => $data[$i]->bussiness,
                    'vendedor' => $arrayIds[$i][0]->vendedor,
                    'thumbImg' => $arrayIds[$i][0]->thumbImg,
                    'logo' => $arrayIds[$i][0]->logo,
                    'estado' => $data[$i]->descripcion,
                    'traduccion' => $data[$i]->traduccion,
                    'cliente_isocurrency' => $cliente_isocurrency,
                    'cliente_tc' => $cliente_tc,
                    'business_isocurrency' => $business_isocurrency,
                    'business_tc' => $business_tc,
                ];
                array_push($arrayData, $object);
            }
            $lastobject = (object) [
                'page' => $page,
                'next' => $next,
                'results' => $arrayData,
            ];
            array_push($arrayDataandcount,$lastobject);
            return response()->json($arrayDataandcount); 
        } else {
            return false;
        }         
    }
    public function show5_2(Request $request){
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
        // variables
        $userIdvendedor;
        $data = DB::table('ordenestados')
            ->join('ordencambiaestados', 'ordenestados.id', '=', 'ordencambiaestados.ordenestadoId')
            ->join('ordens', 'ordens.id', '=', 'ordencambiaestados.ordenId')
            ->join('transaccions', 'transaccions.ordenId', '=', 'ordens.id')
            ->join('usuarios', 'usuarios.id', '=', 'transaccions.userIdcomprador')
            ->join('pagos', 'pagos.ordenId', '=', 'ordens.id')
            ->join('businesses', 'businesses.userId', '=', 'transaccions.userIdvendedor')
            ->select('pagos.cliente_isocurrency','pagos.cliente_tc','pagos.business_isocurrency','pagos.business_tc','ordens.id', 'ordens.numero', 'ordens.fechaentrega', 'ordens.descripcion as ordendescripcion', 'ordens.total','transaccions.userIdvendedor','businesses.descripcion as bussiness', DB::raw('(SELECT ordenestados.descripcion FROM ordenestados WHERE ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS descripcion'), DB::raw('(SELECT traduccions.descripcion FROM ordenestados INNER JOIN valuesidiomas ON ordenestados.validiomaId = valuesidiomas.id INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON traduccions.idiomaId = idiomas.id WHERE idiomas.id = "'.$idioma.'" and ordenestados.id = MAX(ordencambiaestados.ordenestadoId)) AS traduccion'))
            ->where('usuarios.id_firebase', $id_firebase)
            ->whereRaw('concat(ordens.numero," ",(SELECT businesses.descripcion FROM usuarios INNER JOIN businesses ON businesses.userId = usuarios.id WHERE usuarios.id  = transaccions.userIdvendedor)," ",(SELECT usuarios.name FROM usuarios WHERE usuarios.id  = transaccions.userIdvendedor)) like ?', '%'.$busqueda.'%')
            ->groupBy('ordens.id')
            ->orderBy('ordens.fechaentrega', 'DESC')
            ->get();                  
        if($data){
            $arrayIds = array();
            foreach ($data as $key => $dt) {
                $dataUser = DB::table('usuarios')
                    ->join('businesses', 'businesses.userId', '=', 'usuarios.id')
                    ->select('usuarios.name as vendedor', 'usuarios.photo as thumbImg', 'businesses.logo')
                    ->where('usuarios.id', $dt->userIdvendedor)
                    ->get();
                array_push($arrayIds, $dataUser);     
            }
            $arrayData = array();
            $arrayDataandcount = array();
            for($i = 0; $i<count($arrayIds); $i++){
                if($busqueda != ''){
                    $cliente_isocurrency = 'PEN';
                    if($data[$i]->cliente_isocurrency != ""){
                        $cliente_isocurrency = $data[$i]->cliente_isocurrency;
                    }

                    $cliente_tc = 3.583501;
                    if($data[$i]->cliente_tc != 0){
                        $cliente_tc = $data[$i]->cliente_tc;
                    }

                    $business_isocurrency = 'PEN';
                    if($data[$i]->business_isocurrency != ""){
                        $business_isocurrency = $data[$i]->business_isocurrency;
                    }

                    $business_tc = 1;
                    if($data[$i]->business_tc != 0){
                        $business_tc = $data[$i]->business_tc;
                    }
                    $object = (object) [
                        'ordenid' => $data[$i]->id,
                        'ordentitle' => $data[$i]->numero,
                        'fechaentrega' => $data[$i]->fechaentrega,
                        'ordendescripcion' => $data[$i]->ordendescripcion,
                        'total' => $data[$i]->total,
                        'negocio/profesional' => $data[$i]->bussiness,
                        'vendedor' => $arrayIds[$i][0]->vendedor,
                        'thumbImg' => $arrayIds[$i][0]->thumbImg,
                        'logo' => $arrayIds[$i][0]->logo,
                        'estado' => $data[$i]->descripcion,
                        'traduccion' => $data[$i]->traduccion,
                        'cliente_isocurrency' => $cliente_isocurrency,
                        'cliente_tc' => $cliente_tc,
                        'business_isocurrency' => $business_isocurrency,
                        'business_tc' => $business_tc,
                    ];
                    array_push($arrayData, $object);
                }
            }
            //paginacion
            $pageskip = intval($page) - 1;
            $items = intval($items);  
            $nextpage = $page+1;
            // next - previous
            $next = '';
            $arrayDataPaginado = array_slice($arrayData, (($pageskip) * $items), $items);
            if(count($arrayDataPaginado) > ($items-1)){
                $next = 'https://apolomultimedia-server4.info/BuscarPedido/?id_firebase='.$id_firebase.'&page='.$nextpage.'&items='.$items.'&idioma='.$idioma.'&busqueda='.$busqueda;
            }
            $lastobject = (object) [
                'page' => $page,
                'next' => $next,
                'results' => $arrayDataPaginado,
            ];
            array_push($arrayDataandcount,$lastobject);
            return response()->json($arrayDataandcount); 
        } else {
            return false;
        }         
    }
    
    public function show6($id_firebase,$ordenid){
        // variables
        $object;
        $entregado = '';
        $fechaentregado = '';
        $recibido = '';
        $fecharecibido = '';
        $data = DB::table('ordenestados')
            ->join('ordencambiaestados', 'ordenestados.id', '=', 'ordencambiaestados.ordenestadoId')
            ->join('ordens', 'ordens.id', '=', 'ordencambiaestados.ordenId')
            ->join('transaccions', 'transaccions.ordenId', '=', 'ordens.id')
            ->join('usuarios', 'usuarios.id', '=', 'transaccions.userIdcomprador')
            ->join('pagos', 'pagos.ordenId', '=', 'ordens.id')
            ->join('businesses', 'businesses.userId', '=', 'transaccions.userIdvendedor')
            ->select('pagos.cliente_isocurrency','pagos.cliente_tc','pagos.business_isocurrency','pagos.business_tc','ordens.numero', 'ordens.descripcion as ordendescripcion', 'ordens.fechaentrega', 'ordens.total','businesses.descripcion as bussiness', 'transaccions.userIdcomprador', 'transaccions.userIdvendedor','pagos.fecha','ordenestados.descripcion as estadodescripcion','ordencambiaestados.created_at')
            ->where('usuarios.id_firebase', $id_firebase)
            ->where('ordens.id', $ordenid)
            ->get();
        foreach ($data as $key => $dt) {
            if($dt->estadodescripcion == 'Entregado'){
                $entregado = $dt->estadodescripcion;
                $fechaentregado = $dt->created_at;
            }else if($dt->estadodescripcion == 'Recibido'){
                $recibido = $dt->estadodescripcion;
                $fecharecibido = $dt->created_at;
            }    
            $vendedor = DB::table('usuarios')
                    ->select('usuarios.id_firebase as idFirebasevendedor', 'usuarios.name as vendedor')
                    ->where('usuarios.id', $dt->userIdvendedor)
                    ->get();
            $comprador = DB::table('usuarios')
                    ->select('usuarios.id_firebase as idFirebasecliente', 'usuarios.name as comprador')
                    ->where('usuarios.id', $dt->userIdcomprador)
                    ->get(); 

            $cliente_isocurrency = 'PEN';
            if($dt->cliente_isocurrency != ""){
                $cliente_isocurrency = $dt->cliente_isocurrency;
            }

            $cliente_tc = 3.583501;
            if($dt->cliente_tc != 0){
                $cliente_tc = $dt->cliente_tc;
            }

            $business_isocurrency = 'PEN';
            if($dt->business_isocurrency != ""){
                $business_isocurrency = $dt->business_isocurrency;
            }

            $business_tc = 1;
            if($dt->business_tc != 0){
                $business_tc = $dt->business_tc;
            }

            $object = (object) [
                'ordentitle' => $dt->numero,
                'producto/servicio' => $dt->ordendescripcion,
                'fechaentrega' => $dt->fechaentrega,
                'precio' => $dt->total,
                'idFirebasevendedor' => $vendedor[0]->idFirebasevendedor,
                'negocio/profesional' => $dt->bussiness,
                'idFirebasecliente' => $comprador[0]->idFirebasecliente,
                'cliente' => $comprador[0]->comprador,
                'pago' => $dt->fecha,
                //'marcadoEntregado' => $entregado,
                'fechaentregado' => $fechaentregado,
                //'marcadoRecibido' => $recibido,
                'fecharecibido' => $fecharecibido,
                'cliente_isocurrency' => $cliente_isocurrency,
                'cliente_tc' => $cliente_tc,
                'business_isocurrency' => $business_isocurrency,
                'business_tc' => $business_tc,
            ];         
        }
        if(isset($object)){
            return response()->json($object);
        }else{
            return response()->json(false);
        }  
    }
}
