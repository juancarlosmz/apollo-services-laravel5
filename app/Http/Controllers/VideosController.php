<?php
namespace App\Http\Controllers;
use App\Videos;
use Illuminate\Http\Request;
use App\Http\Requests\VideosRequest;
use Illuminate\Support\Facades\DB;
// Firebase conection
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Storage;
// vimeo
use Vimeo\Vimeo;
class VideosController extends Controller{
    public function create(Request $request,$id_firebase){
        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if($userId){
            $objeto = Videos::create([
                'userId' => $userId[0]->userId,
                'titlevideo' => $request->titlevideo,
                'VideoDescription' => $request->VideoDescription,
                'urlvideo' => $request->urlvideo,
                'urlimagen' => $request->urlimagen,
                'urlvideo_width' => $request->urlvideo_width,
                'urlvideo_height' => $request->urlvideo_height,
                'urlimage_width' => $request->urlimage_width,
                'urlimage_height' => $request->urlimage_height,
                ]);
            if($objeto){
                return response(200);
            }     
        }
    }
    public function show($id_firebase, $idioma){
        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if($userId){
            $results = DB::select( DB::raw('select videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,videos.precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$idioma.'" and videos.userId = "'.$userId[0]->userId.'" ORDER by videos.id;') );     
            //return response()->json($results); 
            if($results){
                // firebase presence
                $reference = $database->getReference('presence');
                $snapshot = $reference->getSnapshot();
                $presence = $snapshot->getValue();
                // firebase users
                $timestatus;
                $arrayData = array();
                for($i = 0; $i<count($results); $i++){
                    foreach ($presence as $key => $prec) { 
                        if($results[$i]->firebaseid == $key){
                            $object = (object) [
                                'videoid' => $results[$i]->id,
                                'titlevideo' => $results[$i]->titlevideo,
                                'VideoDescription' => $results[$i]->VideoDescription,
                                'business' => $results[$i]->business,
                                'direccion' => $results[$i]->direccion,
                                'pais' => $results[$i]->pais,
                                'urlvideo' => $results[$i]->urlvideo,
                                'urlimagen' => $results[$i]->urlimagen,
                                'urlvideo_width' => $results[$i]->urlvideo_width,
                                'urlvideo_height' => $results[$i]->urlvideo_height,
                                'urlimage_width' => $results[$i]->urlimage_width,
                                'urlimage_height' => $results[$i]->urlimage_height,
                                'precio' => $results[$i]->precio,
                                'idpmtype' => $results[$i]->idpmtype,
                                'serviciosId' => $results[$i]->serviciosId,
                                'traduccionservicio' => $results[$i]->traduccionservicio,
                                'firebaseid' => $results[$i]->firebaseid,
                                'timestatus' => $prec,
                                'photo' => $results[$i]->photo,
                            ];
                            array_push($arrayData, $object);
                        }   
                    }
                }
                return response()->json($arrayData);  
            }else{
                return response()->json(false);
            }          
        }             
    }
    public function show_1($id_firebase, $idioma){
        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if($userId){
            $results = DB::select( DB::raw('select (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,videos.precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$idioma.'" and videos.userId = "'.$userId[0]->userId.'" ORDER by videos.id;') );     
            //return response()->json($results); 
            if($results){
                // firebase presence
                $reference = $database->getReference('presence');
                $snapshot = $reference->getSnapshot();
                $presence = $snapshot->getValue();
                // firebase users
                $timestatus;
                $arrayData = array();
                for($i = 0; $i<count($results); $i++){
                    foreach ($presence as $key => $prec) { 
                        if($results[$i]->firebaseid == $key){
                            $object = (object) [
                                'sales' => $results[$i]->sales,
                                'videoid' => $results[$i]->id,
                                'titlevideo' => $results[$i]->titlevideo,
                                'VideoDescription' => $results[$i]->VideoDescription,
                                'business' => $results[$i]->business,
                                'direccion' => $results[$i]->direccion,
                                'pais' => $results[$i]->pais,
                                'urlvideo' => $results[$i]->urlvideo,
                                'urlimagen' => $results[$i]->urlimagen,
                                'urlvideo_width' => $results[$i]->urlvideo_width,
                                'urlvideo_height' => $results[$i]->urlvideo_height,
                                'urlimage_width' => $results[$i]->urlimage_width,
                                'urlimage_height' => $results[$i]->urlimage_height,
                                'precio' => $results[$i]->precio,
                                'idpmtype' => $results[$i]->idpmtype,
                                'serviciosId' => $results[$i]->serviciosId,
                                'traduccionservicio' => $results[$i]->traduccionservicio,
                                'firebaseid' => $results[$i]->firebaseid,
                                'timestatus' => $prec,
                                'photo' => $results[$i]->photo,
                            ];
                            array_push($arrayData, $object);
                        }   
                    }
                }
                return response()->json($arrayData);  
            }else{
                return response()->json(false);
            }          
        }             
    }
    public function shownew(Request $request){

        $request->validate([
            'items' => 'required',
            'page' => 'required',
            'id_firebase' => 'required',
            'language' => 'required',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $distance = 10000; // Sitios que se encuentren en un radio de 1000KM
        $id_firebase = $request->id_firebase;
        $idioma = $request->language;
        $page = $request->page;
        $items = $request->items;

        //paginacion
        $pageskip = intval($page) - 1;
        $items = intval($items);

        // la formula
        $earthRadius = 6371;
        $objeto1 = array();
        // Los angulos para cada dirección
        $cardinalCoords = array('north' => '0', 'south' => '180', 'east' => '90', 'west' => '270');
        $rLat = deg2rad($lat);
        $rLng = deg2rad($lng);
        $rAngDist = $distance/$earthRadius;
        foreach ($cardinalCoords as $name => $angle){
            $rAngle = deg2rad($angle);
            $rLatB = asin(sin($rLat) * cos($rAngDist) + cos($rLat) * sin($rAngDist) * cos($rAngle));
            $rLonB = $rLng + atan2(sin($rAngle) * sin($rAngDist) * cos($rLat), cos($rAngDist) - sin($rLat) * sin($rLatB));
            $objeto1[$name] = array('lat' => (float) rad2deg($rLatB), 
                                    'lng' => (float) rad2deg($rLonB));
        }
        $min_lat  = $objeto1['south']['lat'];
        $max_lat = $objeto1['north']['lat'];
        $min_lng = $objeto1['west']['lng'];
        $max_lng = $objeto1['east']['lng'];
        // end la formula

        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        // 
        //$items = $items + 1;
        $myitems = $items + 1;
        $pageskip = ($pageskip) * $items;

        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();       
        if($userId){

            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic,(SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,videos.precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo , IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and videos.userId = "'.$userId[0]->userId.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN '.$min_lng.' AND '.$max_lng.') HAVING distance  < '.$distance.' ORDER BY distance ASC LIMIT '.$myitems.' OFFSET '.$pageskip.';') );     

            //return response()->json($results);

            $nextpage = $page+1;
            // next - previous
            $next = '';
            if(count($results) > $items){
                $next = 'https://apolomultimedia-server4.info/videosbyuser?items='.$items.'&page='.$nextpage.'&id_firebase='.$id_firebase.'&language='.$idioma.'&lat='.$lat.'&lng='.$lng;
            } 
            unset($results[$items]); 
            
            if($results){
                // firebase presence
                $reference = $database->getReference('presence');
                $snapshot = $reference->getSnapshot();
                $presence = $snapshot->getValue();
                // firebase users
                $timestatus;
                $arrayData = array();
                $arrayDataandcount = array();
                for($i = 0; $i<count($results); $i++){
                    foreach ($presence as $key => $prec) { 
                        if($results[$i]->firebaseid == $key){

                            $object = (object) [
                                'traduccionpublic' => $results[$i]->traduccionpublic,
                                'sales' => $results[$i]->sales,
                                'videoid' => $results[$i]->id,
                                'titlevideo' => $results[$i]->titlevideo,
                                'VideoDescription' => $results[$i]->VideoDescription,
                                'business' => $results[$i]->business,
                                'direccion' => $results[$i]->direccion,
                                'pais' => $results[$i]->pais,
                                'urlvideo' => $results[$i]->urlvideo,
                                'urlimagen' => $results[$i]->urlimagen,
                                'urlvideo_width' => $results[$i]->urlvideo_width,
                                'urlvideo_height' => $results[$i]->urlvideo_height,
                                'urlimage_width' => $results[$i]->urlimage_width,
                                'urlimage_height' => $results[$i]->urlimage_height,
                                'precio' => $results[$i]->precio,
                                'idpmtype' => $results[$i]->idpmtype,
                                'serviciosId' => $results[$i]->serviciosId,
                                'traduccionservicio' => $results[$i]->traduccionservicio,
                                'firebaseid' => $results[$i]->firebaseid,
                                'timestatus' => $prec,
                                'photo' => $results[$i]->photo,
                                'logo' => $results[$i]->logo,
                                'distance' => $results[$i]->distance,
                            ];
                            array_push($arrayData, $object);

                        }   
                    }
                }
                $lastobject = (object) [
                    'page' => $page,
                    'next' => $next,
                    'results' => $arrayData,
                ];
                array_push($arrayDataandcount,$lastobject);
                return response()->json($arrayDataandcount); 
            }else{
                return response()->json(false);
            }          
        }             
    }

    public function shownewadmin(Request $request){

        $request->validate([
            'items' => 'required',
            'page' => 'required',
            'id_firebase' => 'required',
            'language' => 'required',
        ]);

        $id_firebase = $request->id_firebase;
        $idioma = $request->language;
        $page = $request->page;
        $items = $request->items;

        //paginacion
        $pageskip = intval($page) - 1;
        $items = intval($items);

        //$items = $items + 1;
        $myitems = $items + 1;
        $pageskip = ($pageskip) * $items;

        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();       
        if($userId){
            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = videos.public and idiomas.id = "'.$idioma.'") as traduccionpublic,videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,videos.precio,videos.userId,videos.idpmtype,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$idioma.'" and videos.userId = "'.$userId[0]->userId.'" ORDER BY videos.id DESC, videos.public DESC LIMIT '.$myitems.' OFFSET '.$pageskip.';') );

            $nextpage = $page+1;
            // next - previous
            $next = '';
            if(count($results) > $items){
                $next = 'https://apolomultimedia-server4.info/videosbyuseradmin?items='.$items.'&page='.$nextpage.'&id_firebase='.$id_firebase.'&language='.$idioma;
            } 
            unset($results[$items]); 
            
            if($results){
                $arrayData = array();
                $arrayDataandcount = array();
                for($i = 0; $i<count($results); $i++){ 

                        $object = (object) [
                            'traduccionpublic' => $results[$i]->traduccionpublic,
                            'videoid' => $results[$i]->id,
                            'titlevideo' => $results[$i]->titlevideo,
                            'VideoDescription' => $results[$i]->VideoDescription,
                            'urlvideo' => $results[$i]->urlvideo,
                            'urlimagen' => $results[$i]->urlimagen,
                            'urlvideo_width' => $results[$i]->urlvideo_width,
                            'urlvideo_height' => $results[$i]->urlvideo_height,
                            'urlimage_width' => $results[$i]->urlimage_width,
                            'urlimage_height' => $results[$i]->urlimage_height,
                            'precio' => $results[$i]->precio,
                            'idpmtype' => $results[$i]->idpmtype,
                            'serviciosId' => $results[$i]->serviciosId,
                            'traduccionservicio' => $results[$i]->traduccionservicio,
                        ];
                        array_push($arrayData, $object);

                }
                $lastobject = (object) [
                    'page' => $page,
                    'next' => $next,
                    'results' => $arrayData,
                ];
                array_push($arrayDataandcount,$lastobject);
                return response()->json($arrayDataandcount[0]); 
            }else{
                return response()->json([
                    'page' => 1,
                    'next' => '',
                    'results' => [],
                  ]);
            }          
        }             
    }


    public function shownewbusqueda(Request $request){

        $request->validate([
            'items' => 'required',
            'page' => 'required',
            'id_firebase' => 'required',
            'language' => 'required',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $distance = 10000; // Sitios que se encuentren en un radio de 1000KM
        $id_firebase = $request->id_firebase;
        $idioma = $request->language;
        $page = $request->page;
        $items = $request->items;
        //
        $busqueda = $request->busqueda;

        //paginacion
        $pageskip = intval($page) - 1;
        $items = intval($items);

        // la formula
        $earthRadius = 6371;
        $objeto1 = array();
        // Los angulos para cada dirección
        $cardinalCoords = array('north' => '0', 'south' => '180', 'east' => '90', 'west' => '270');
        $rLat = deg2rad($lat);
        $rLng = deg2rad($lng);
        $rAngDist = $distance/$earthRadius;
        foreach ($cardinalCoords as $name => $angle){
            $rAngle = deg2rad($angle);
            $rLatB = asin(sin($rLat) * cos($rAngDist) + cos($rLat) * sin($rAngDist) * cos($rAngle));
            $rLonB = $rLng + atan2(sin($rAngle) * sin($rAngDist) * cos($rLat), cos($rAngDist) - sin($rLat) * sin($rLatB));
            $objeto1[$name] = array('lat' => (float) rad2deg($rLatB), 
                                    'lng' => (float) rad2deg($rLonB));
        }
        $min_lat  = $objeto1['south']['lat'];
        $max_lat = $objeto1['north']['lat'];
        $min_lng = $objeto1['west']['lng'];
        $max_lng = $objeto1['east']['lng'];
        // end la formula

        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        // 
        //$items = $items + 1;
        $myitems = $items + 1;
        $pageskip = ($pageskip) * $items;

        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if($userId){

            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic, (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,videos.precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo, (6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))) AS distance FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and videos.userId = "'.$userId[0]->userId.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN '.$min_lng.' AND '.$max_lng.') HAVING distance  < '.$distance.' and concat(videos.titlevideo," ",businesses.descripcion," ", videos.VideoDescription) like "%'.$busqueda.'%" ORDER BY distance ASC;' ));     
            
            if($results){
                // firebase presence
                $reference = $database->getReference('presence');
                $snapshot = $reference->getSnapshot();
                $presence = $snapshot->getValue();
                // firebase users
                $timestatus;
                $arrayData = array();
                $arrayDataandcount = array();
                for($i = 0; $i<count($results); $i++){
                    foreach ($presence as $key => $prec) { 
                        if($results[$i]->firebaseid == $key){
                            if($busqueda != ''){

                                $object = (object) [
                                    'traduccionpublic' => $results[$i]->traduccionpublic,
                                    'sales' => $results[$i]->sales,
                                    'videoid' => $results[$i]->id,
                                    'titlevideo' => $results[$i]->titlevideo,
                                    'VideoDescription' => $results[$i]->VideoDescription,
                                    'business' => $results[$i]->business,
                                    'direccion' => $results[$i]->direccion,
                                    'pais' => $results[$i]->pais,
                                    'urlvideo' => $results[$i]->urlvideo,
                                    'urlimagen' => $results[$i]->urlimagen,
                                    'urlvideo_width' => $results[$i]->urlvideo_width,
                                    'urlvideo_height' => $results[$i]->urlvideo_height,
                                    'urlimage_width' => $results[$i]->urlimage_width,
                                    'urlimage_height' => $results[$i]->urlimage_height,
                                    'precio' => $results[$i]->precio,
                                    'idpmtype' => $results[$i]->idpmtype,
                                    'serviciosId' => $results[$i]->serviciosId,
                                    'traduccionservicio' => $results[$i]->traduccionservicio,
                                    'firebaseid' => $results[$i]->firebaseid,
                                    'timestatus' => $prec,
                                    'photo' => $results[$i]->photo,
                                    'logo' => $results[$i]->logo,
                                    'distance' => $results[$i]->distance,
                                ];
                                array_push($arrayData, $object);

                            }else{

                            }
                        }   
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
                    $next = 'https://apolomultimedia-server4.info/videosbusqueda?items='.$items.'&page='.$nextpage.'&id_firebase='.$id_firebase.'&language='.$idioma.'&lat='.$lat.'&lng='.$lng.'&busqueda='.$busqueda;
                } 
                $lastobject = (object) [
                    'page' => $page,
                    'next' => $next,
                    'results' => $arrayDataPaginado,
                ];
                array_push($arrayDataandcount,$lastobject);
                return response()->json($arrayDataandcount);
            }else{
                return response()->json(false);
            }          
        }             
    }

    public function showordenespagadas(Request $request){

        $request->validate([
            'id_firebase' => 'required',
            'language' => 'required',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $distance = 10000; // Sitios que se encuentren en un radio de 1000KM
        $id_firebase = $request->id_firebase;
        $idioma = $request->language;
        //return response()->json($lat); 
        // la formula
        $earthRadius = 6371;
        $objeto1 = array();
        // Los angulos para cada dirección
        $cardinalCoords = array('north' => '0', 'south' => '180', 'east' => '90', 'west' => '270');
        $rLat = deg2rad($lat);
        $rLng = deg2rad($lng);
        $rAngDist = $distance/$earthRadius;
        foreach ($cardinalCoords as $name => $angle){
            $rAngle = deg2rad($angle);
            $rLatB = asin(sin($rLat) * cos($rAngDist) + cos($rLat) * sin($rAngDist) * cos($rAngle));
            $rLonB = $rLng + atan2(sin($rAngle) * sin($rAngDist) * cos($rLat), cos($rAngDist) - sin($rLat) * sin($rLatB));
            $objeto1[$name] = array('lat' => (float) rad2deg($rLatB), 
                                    'lng' => (float) rad2deg($rLonB));
        }
        $min_lat  = $objeto1['south']['lat'];
        $max_lat = $objeto1['north']['lat'];
        $min_lng = $objeto1['west']['lng'];
        $max_lng = $objeto1['east']['lng'];
        // end la formula
        //return response()->json($objeto1); 

        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if($userId){

            $results1 = DB::select( DB::raw('select DISTINCT (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales,businesses.descripcion as business,businesses.direccion,businesses.pais,businesses.ciudad,businesses.lat,businesses.lng,businesses.Zip,businesses.details,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo, businesses.logo FROM businesses INNER JOIN usuarios ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE  idiomas.id = "'.$idioma.'" and businesses.userId = "'.$userId[0]->userId.'" ') ); 

            $results2 = DB::select( DB::raw('select ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) AS distance FROM businesses WHERE businesses.userId = "'.$userId[0]->userId.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN '.$min_lng.' AND '.$max_lng.') HAVING distance  < '.$distance.' ORDER BY distance ASC;') ); 
             
            // firebase presence
            $reference = $database->getReference('presence');
            $snapshot = $reference->getSnapshot();
            $presence = $snapshot->getValue();
            // firebase users
            $timestatus;
            $arrayData = array();
            //return response()->json(count($arrayData)); 
            $distanceoficial = 0;
            if(count($arrayData) == 0){
                $distanceoficial = 0;
            }else{
                $distanceoficial = $results2[0]->distance;
            }
            //return response($lat.'**'.$lng .'**'.$userId[0]->userId.'**'.$min_lat.'**'.$max_lat.'**'.$min_lng.'**'.$max_lng);
            if($results1){

                
                for($i = 0; $i<count($results1); $i++){
                    foreach ($presence as $key => $prec) { 
                        if($results1[$i]->firebaseid == $key){
                            $details = '';
                            $direccion = '';
                            $pais = '';
                            $ciudad = '';
                            $Zip = '';
                            if($results1[$i]->details == null){
                                $details = '';
                            }else{
                                $details = $results1[$i]->details;
                            }
                            if($results1[$i]->direccion == null){
                                $direccion = '';
                            }else{
                                $direccion = $results1[$i]->direccion;
                            }
                            if($results1[$i]->pais == null){
                                $pais = '';
                            }else{
                                $pais = $results1[$i]->pais;
                            }
                            if($results1[$i]->ciudad == null){
                                $ciudad = '';
                            }else{
                                $ciudad = $results1[$i]->ciudad;
                            }
                            if($results1[$i]->Zip == null){
                                $Zip = '';
                            }else{
                                $Zip = $results1[$i]->Zip;
                            }
                            $object = (object) [
                                'sales' => $results1[$i]->sales,
                                'business' => $results1[$i]->business,
                                'details' => $details,
                                'direccion' => $direccion,
                                'pais' => $pais,
                                'ciudad' => $ciudad,
                                'zip' => $Zip,
                                'lat' => $results1[$i]->lat,
                                'lng' => $results1[$i]->lng,
                                'serviciosId' => $results1[$i]->serviciosId,
                                'traduccionservicio' => $results1[$i]->traduccionservicio,
                                'firebaseid' => $results1[$i]->firebaseid,
                                'timestatus' => $prec,
                                'photo' => $results1[$i]->photo,
                                'logo' => $results1[$i]->logo,
                                'distance' => $distanceoficial,
                            ];
                            array_push($arrayData, $object);
                        }   
                    }
                }
                return response()->json($arrayData); 
            } else {
                return response()->json(false); 
            }
            
        }             
    }

    public function show2($idservicio,$idioma){
        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        if($idservicio == 0){
            $results = DB::select( DB::raw('select (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.ubicacion,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$idioma.'" ORDER by videos.id;') );   
        }else{ 
            $results = DB::select( DB::raw('select (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.ubicacion,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$idioma.'" and serviciousuarios.serviciosId = "'.$idservicio.'" ORDER by videos.id;') );   
        }
        //return response()->json($results); 
        if($results){
            // firebase presence
            $reference = $database->getReference('presence');
            $snapshot = $reference->getSnapshot();
            $presence = $snapshot->getValue();
            // firebase users
            $timestatus;
            $arrayData = array();
            for($i = 0; $i<count($results); $i++){
                foreach ($presence as $key => $prec) { 
                    if($results[$i]->firebaseid == $key){
                        $object = (object) [
                            'sales' => $results[$i]->sales,
                            'videoid' => $results[$i]->id,
                            'titlevideo' => $results[$i]->titlevideo,
                            'VideoDescription' => $results[$i]->VideoDescription,
                            'business' => $results[$i]->business,
                            'ubicacion' => $results[$i]->ubicacion,
                            'direccion' => $results[$i]->direccion,
                            'pais' => $results[$i]->pais,
                            'urlvideo' => $results[$i]->urlvideo,
                            'urlimagen' => $results[$i]->urlimagen,
                            'precio' => $results[$i]->precio,
                            'idpmtype' => $results[$i]->idpmtype,
                            'serviciosId' => $results[$i]->serviciosId,
                            'traduccionservicio' => $results[$i]->traduccionservicio,
                            'firebaseid' => $results[$i]->firebaseid,
                            'timestatus' => $prec,
                            'photo' => $results[$i]->photo,
                        ];
                        array_push($arrayData, $object);
                    }   
                }
            }
            return response()->json($arrayData);  
        }else{
            return response()->json(false);
        }
    }
    public function show2_BACKUP_NOES($idservicio,$idioma){
        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        if($idservicio == 0){
            $results = DB::select( DB::raw('select videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.precio,videos.userId,businesses.descripcion as business,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$idioma.'"') );   
        }else{
            $results = DB::select( DB::raw('select videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.precio,videos.userId,businesses.descripcion as business,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$idioma.'" and serviciousuarios.serviciosId = "'.$idservicio.'";') );   
        }
        //return response()->json($results); 
        if($results){
            $userfbphoto = '';
            $presence = '';
            $timestatus;
            $arrayData = array();
            $timestatus = '';
            for($i = 0; $i<count($results); $i++){
                $userfbphoto = $database->getReference('users/'.$results[$i]->firebaseid.'/photo')->getValue();
                $presence = $database->getReference('presence/'.$results[$i]->firebaseid)->getValue();
                $object = (object) [
                    'titlevideo' => $results[$i]->titlevideo,
                    'VideoDescription' => $results[$i]->VideoDescription,
                    'business' => $results[$i]->business,
                    'urlvideo' => $results[$i]->urlvideo,
                    'urlimagen' => $results[$i]->urlimagen,
                    'precio' => $results[$i]->precio,
                    'serviciosId' => $results[$i]->serviciosId,
                    'traduccionservicio' => $results[$i]->traduccionservicio,
                    'firebaseid' => $results[$i]->firebaseid,
                    'timestatus' => $presence,
                    'photo' => $userfbphoto,
                ];
                array_push($arrayData, $object);
            }
            return response()->json($arrayData);  
        }else{
            return response()->json(false);
        }
    }

    public function show2_1($idservicio,$idioma){
        // vimeo conexion
        $client = new Vimeo("775c22cdd9fc4659ce5e3d8b60983ea494d5f651", "EvxF5vcxCLcmYDoTdY14Je1WuDG7fVolfehkXEOi9ZtkWr1NXcrCwmdlrOSYj6uwKwkFvZGHRXmHW8NzD8ub5wNItDpoQBbs062//5f+8FNWUnXU2sxrOJrdjMUpqJ9a", "9a57070414710af364956455ef45bd7f");
        $response = $client->request('/me/videos', array(), 'GET');
        $datavimeo = $response['body']['data'];
        $arrayVimeo = array();

        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        if($idservicio == 0){
            $results = DB::select( DB::raw('select videos.titlevideo,videos.urlvideo,videos.urlimagen,videos.precio,videos.userId,businesses.descripcion as business,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$idioma.'"') );   
        }else{
            $results = DB::select( DB::raw('select videos.titlevideo,videos.urlvideo,videos.urlimagen,videos.precio,videos.userId,businesses.descripcion as business,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE idiomas.id = "'.$idioma.'" and serviciousuarios.serviciosId = "'.$idservicio.'";') );   
        }
        
        if($results){
            // firebase presence
            $reference = $database->getReference('presence');
            $snapshot = $reference->getSnapshot();
            $presence = $snapshot->getValue();
            // firebase users
            $reference2 = $database->getReference('users');
            $snapshot2 = $reference2->getSnapshot();
            $userfb = $snapshot2->getValue();
            $thumbImg;
            $timestatus;
            $arrayData = array();
            for($i = 0; $i<count($results); $i++){
                foreach ($presence as $key => $prec) { 
                    if($results[$i]->firebaseid == $key){
                        foreach ($userfb as $key => $usfb) { 
                            if($results[$i]->firebaseid == $key){
                                for($j = 0; $j<count($usfb); $j++){
                                    if(array_keys($usfb)[$j] == 'photo'){
                                        $thumbImg = array_values($usfb)[$j]; 
                                    }
                                }   
                            }        
                        }
                        $videovimeo = $datavimeo[$i]['uri'];
                        $idvideovimeo = substr($videovimeo,7,11);

/*
                        foreach ($datavimeo as $keyvim => $vim) {
                            $videovimeo = $datavimeo[$keyvim]['uri'];
                        }*/
                        $object = (object) [
                            'titlevideo' => $results[$i]->titlevideo,
                            'business' => $results[$i]->business,
                            'urlvideo' => 'https://player.vimeo.com/video'.$idvideovimeo,
                            'urlimagen' => $results[$i]->urlimagen,
                            'precio' => $results[$i]->precio,
                            //'userId' => $results[$i]->userId,
                            'serviciosId' => $results[$i]->serviciosId,
                            'traduccionservicio' => $results[$i]->traduccionservicio,
                            'firebaseid' => $results[$i]->firebaseid,
                            'timestatus' => $prec,
                            'photo' => $thumbImg,
                        ];
                        array_push($arrayData, $object);
                    }   
                }
            }
            return response()->json($arrayData);  
        }else{
            return response()->json(false);
        }
    }
    public function show3(){
        //$client = new Vimeo("{client_id}", "{client_secret}", "{access_token}");
        $client = new Vimeo("775c22cdd9fc4659ce5e3d8b60983ea494d5f651", "EvxF5vcxCLcmYDoTdY14Je1WuDG7fVolfehkXEOi9ZtkWr1NXcrCwmdlrOSYj6uwKwkFvZGHRXmHW8NzD8ub5wNItDpoQBbs062//5f+8FNWUnXU2sxrOJrdjMUpqJ9a", "9a57070414710af364956455ef45bd7f");
        $response = $client->request('/me/videos', array(), 'GET');
        $data = $response['body']['data'];
        
        $arrayVimeo = array();
        $name = '';
        $duration = '';
        $link = '';
        $link2 = '';
        $link3 = '';
        $size = '';
        // new data
        $width = '';
        $height = '';
        
        $quality = '';

        $myheight = 0;
        $mywidth = 0;
        $mylink2 = '';
        $mysize = '';

        foreach ($data as $keyvim => $vim) {
            $duration = $data[$keyvim]['duration'];
            $name = $data[$keyvim]['name'];
            $link = $data[$keyvim]['link'];
            foreach ($data[$keyvim]['files'] as $keyfile => $file) {
                $quality = $data[$keyvim]['files'][$keyfile]['quality'];
                if($quality == 'sd'){
                    $height = $data[$keyvim]['files'][$keyfile]['height'];
                    if($height == 960){
                        $myheight = $data[$keyvim]['files'][$keyfile]['height'];
                        $mywidth = $data[$keyvim]['files'][$keyfile]['width'];
                        $mylink2 = $data[$keyvim]['files'][$keyfile]['link'];
                        $mysize = round(($data[$keyvim]['files'][$keyfile]['size']/1024/1024), 2).'M';
                    }else{
                        
                        
                        if($height == 640){
                            if($height < $myheight){
                                $height = $myheight;
                                $width = $mywidth;
                                $link2 = $mylink2;
                                $size = $mysize;
                            }else{
                                $myheight = $data[$keyvim]['files'][$keyfile]['height'];
                                $mywidth = $data[$keyvim]['files'][$keyfile]['width'];
                                $mylink2 = $data[$keyvim]['files'][$keyfile]['link'];
                                $mysize = round(($data[$keyvim]['files'][$keyfile]['size']/1024/1024), 2).'M';
                            }
                            
                        } 
                    }

                }
            }
            foreach ($data[$keyvim]['pictures']['sizes'] as $keypic => $pic) {
                if($data[$keyvim]['pictures']['sizes'][$keypic]['width'] == 1280 and $data[$keyvim]['pictures']['sizes'][$keypic]['height'] == 720){
                    $link3 = $data[$keyvim]['pictures']['sizes'][$keypic]['link'];
                    $linksplit = explode('_',$link3);
                    $link3_1 = $linksplit[0].'.jpg';

                }
            }
            $objectvimeo = (object) [
                'name' => $name,
                'link' => $link,
                'urlvideo' => $mylink2,
                'size' => $mysize,
                'videosize' => $duration,
                'urlimagen' => $link3_1,
                'width' => $mywidth,
                'height' => $myheight,
            ];
            array_push($arrayVimeo, $objectvimeo);  
        }
        return response()->json($arrayVimeo);
    }
    public function onevimeo(Request $request){

        $request->validate([
            'video_id' => 'required',
            'active' => 'required',
            'time' => 'required',
        ]);
        $video_id = $request->video_id;
        $active = $request->active;
        $time = $request->time;
        
        $idvimeo = '/videos/450262517';
        $active = true;
        $time = 10;
        //$client = new Vimeo("{client_id}", "{client_secret}", "{access_token}");
        $client = new Vimeo("775c22cdd9fc4659ce5e3d8b60983ea494d5f651", "EvxF5vcxCLcmYDoTdY14Je1WuDG7fVolfehkXEOi9ZtkWr1NXcrCwmdlrOSYj6uwKwkFvZGHRXmHW8NzD8ub5wNItDpoQBbs062//5f+8FNWUnXU2sxrOJrdjMUpqJ9a", "9a57070414710af364956455ef45bd7f");
        $response = $client->request('/me'.$idvimeo, array(), 'GET');
        $responsepictures = $client->request('/videos/'.$video_id.'/pictures', array(), 'GET');

        $duration = $response['body']['name'];
        return response()->json($responsepictures);
       
        $arrayVimeo = array();
        $name = '';
        $link = '';
        $link2 = '';
        $link3 = '';
        $link3_1 = '';
        $size = '';
        // new data
        $width = '';
        $height = '';

        $name = $response['body']['name'];
        $link = $response['body']['link'];
        foreach ($response['body']['files'] as $keyfile => $file) {
            if($response['body']['files'][$keyfile]['quality'] != 'hls'){
                if($response['body']['files'][$keyfile]['height'] == 960){
                    $link2 = $response['body']['files'][$keyfile]['link'];
                    $size = round(($response['body']['files'][$keyfile]['size']/1024/1024), 2).'M';
                    $width = $response['body']['files'][$keyfile]['width'];
                    $height = $response['body']['files'][$keyfile]['height'];
                }else if($response['body']['files'][$keyfile]['height'] == 640){
                    $link2 = $response['body']['files'][$keyfile]['link'];
                    $size = round(($response['body']['files'][$keyfile]['size']/1024/1024), 2).'M';
                    $width = $response['body']['files'][$keyfile]['width'];
                    $height = $response['body']['files'][$keyfile]['height'];
                }
            }
        }
        foreach ($response['body']['pictures']['sizes'] as $keypic => $pic) {
            if($response['body']['pictures']['sizes'][$keypic]['width'] == 1280 and $response['body']['pictures']['sizes'][$keypic]['height'] == 720){
                $link3 = $response['body']['pictures']['sizes'][$keypic]['link'];
                $linksplit = explode('_',$link3);
                $link3_1 = $linksplit[0].'.jpg';
            }
        }
        $objectvimeo = (object) [
            'name' => $name,
            'link' => $link,
            'urlvideo' => $link2,
            'size' => $size,
            'videosize' => $duration,
            'urlimagen' => $link3_1,
            'width' => $width,
            'height' => $height,
        ];
        array_push($arrayVimeo, $objectvimeo);  
        
        return response()->json($arrayVimeo[0]);

    }
    public function crearonevimeo(Request $request){

        $request->validate([
            'video_id' => 'required',
        ]);
        $video_id = $request->video_id;
       
        //$client = new Vimeo("{client_id}", "{client_secret}", "{access_token}");
        $client = new Vimeo("775c22cdd9fc4659ce5e3d8b60983ea494d5f651", "EvxF5vcxCLcmYDoTdY14Je1WuDG7fVolfehkXEOi9ZtkWr1NXcrCwmdlrOSYj6uwKwkFvZGHRXmHW8NzD8ub5wNItDpoQBbs062//5f+8FNWUnXU2sxrOJrdjMUpqJ9a", "9a57070414710af364956455ef45bd7f");
        
        $responsepictures = $client->request('/videos/'.$video_id.'/pictures', array('time' => '0'), 'POST');
        $newlink = $responsepictures['body']['sizes'][0]['link'];
        return response()->json($newlink);
       
       

    }
    public function show4(){
        //$client = new Vimeo("{client_id}", "{client_secret}", "{access_token}");
        $client = new Vimeo("775c22cdd9fc4659ce5e3d8b60983ea494d5f651", "EvxF5vcxCLcmYDoTdY14Je1WuDG7fVolfehkXEOi9ZtkWr1NXcrCwmdlrOSYj6uwKwkFvZGHRXmHW8NzD8ub5wNItDpoQBbs062//5f+8FNWUnXU2sxrOJrdjMUpqJ9a", "9a57070414710af364956455ef45bd7f");
        $response = $client->request('/me/videos', array(), 'GET');
        $data = $response['body']['data'];
        return response()->json($data);
    }
    
    public function update(Request $request, $id){
        $objeto = Videos::findOrFail($id);
        $objeto->fill($request->all());
        $objeto->push();
        if($objeto){
            return response(200);
        }
    }
    public function destroy($id){
        $objeto = Videos::findOrFail($id);
        $objeto->delete();
        if($objeto){
            return response(200);
        }
    }
    
    public function haversine(Request $request){
        $request->validate([
            'latitudeFrom' => 'required',
            'longitudeFrom' => 'required',
            'latitudeTo' => 'required',
            'longitudeTo' => 'required',
        ]);
        $latitudeFrom = $request->latitudeFrom;
        $longitudeFrom = $request->longitudeFrom;
        $latitudeTo = $request->latitudeTo;
        $longitudeTo = $request->longitudeTo;
        $earthRadius = 6371000;

        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
           
        return response()->json($angle * $earthRadius);
        // return response()->json($latitudeFrom);
    }
    function getBoundaries(Request $request){
        $request->validate([
            'lat' => 'required',
            'lng' => 'required',
            'distance' => 'required',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $distance = $request->distance; // Sitios que se encuentren en un radio de 1KM

        $earthRadius = 6371;
        $objeto1 = array();
        // Los angulos para cada dirección
        $cardinalCoords = array('north' => '0', 'south' => '180', 'east' => '90', 'west' => '270');
        $rLat = deg2rad($lat);
        $rLng = deg2rad($lng);
        $rAngDist = $distance/$earthRadius;
        foreach ($cardinalCoords as $name => $angle){
            $rAngle = deg2rad($angle);
            $rLatB = asin(sin($rLat) * cos($rAngDist) + cos($rLat) * sin($rAngDist) * cos($rAngle));
            $rLonB = $rLng + atan2(sin($rAngle) * sin($rAngDist) * cos($rLat), cos($rAngDist) - sin($rLat) * sin($rLatB));
            $objeto1[$name] = array('lat' => (float) rad2deg($rLatB), 
                                    'lng' => (float) rad2deg($rLonB));
        }
        $min_lat  = $objeto1['south']['lat'];
        $max_lat = $objeto1['north']['lat'];
        $min_lng = $objeto1['west']['lng'];
        $max_lng = $objeto1['east']['lng'];

        $results = DB::select( DB::raw('SELECT *, (6371 * ACOS(SIN(RADIANS(lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(lng - ' . $lng . '))* COS(RADIANS(lat))* COS(RADIANS(' . $lat . ')))) AS distance FROM businesses WHERE (lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (lng BETWEEN ' . $min_lng. ' AND ' . $max_lng. ') HAVING distance  < ' . $distance . ' ORDER BY distance ASC;') );

        return response()->json($results);
    }

    public function showallvideos(Request $request){

        $request->validate([
            'items' => 'required',
            'page' => 'required',
            'idService' => 'required',
            'language' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $distance = 10000; // Sitios que se encuentren en un radio de 1000KM
        $idservicio = $request->idService;
        $idioma = $request->language;
        $page = $request->page;
        $items = $request->items;

        //paginacion
        $pageskip = intval($page) - 1;
        $items = intval($items);

        // la formula
        $earthRadius = 6371;
        $objeto1 = array();
        // Los angulos para cada dirección
        $cardinalCoords = array('north' => '0', 'south' => '180', 'east' => '90', 'west' => '270');
        $rLat = deg2rad($lat);
        $rLng = deg2rad($lng);
        $rAngDist = $distance/$earthRadius;
        foreach ($cardinalCoords as $name => $angle){
            $rAngle = deg2rad($angle);
            $rLatB = asin(sin($rLat) * cos($rAngDist) + cos($rLat) * sin($rAngDist) * cos($rAngle));
            $rLonB = $rLng + atan2(sin($rAngle) * sin($rAngDist) * cos($rLat), cos($rAngDist) - sin($rLat) * sin($rLatB));
            $objeto1[$name] = array('lat' => (float) rad2deg($rLatB), 
                                    'lng' => (float) rad2deg($rLonB));
        }
        $min_lat  = $objeto1['south']['lat'];
        $max_lat = $objeto1['north']['lat'];
        $min_lng = $objeto1['west']['lng'];
        $max_lng = $objeto1['east']['lng'];
        // end la formula

        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        //$items = $items + 1;
        $myitems = $items + 1;
        $pageskip = ($pageskip) * $items;
        if($idservicio == 0){
            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic,(SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,videos.precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo, IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN '.$min_lng.' AND '.$max_lng.') HAVING distance  < '.$distance.' ORDER BY distance ASC LIMIT '.$myitems.' OFFSET '.$pageskip.';')); 
        }else{ 
            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic, (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,videos.precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo, IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and serviciousuarios.serviciosId = "'.$idservicio.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN ' . $min_lng. ' AND ' . $max_lng. ') HAVING distance  < ' . $distance . ' ORDER BY distance ASC LIMIT '.$myitems.' OFFSET '.$pageskip.';'));   
        }

        //  
        //return response()->json($results);

        $nextpage = $page+1;
        // next - previous
        $next = '';
        if(count($results) > $items){
            $next = 'https://apolomultimedia-server4.info/allvideos?items='.$items.'&page='.$nextpage.'&idService='.$idservicio.'&language='.$idioma.'&lat='.$lat.'&lng='.$lng;
        } 
        unset($results[$items]); 

        if($results){
            // firebase presence
            $reference = $database->getReference('presence');
            $snapshot = $reference->getSnapshot();
            $presence = $snapshot->getValue();
            // firebase users
            $timestatus;
            $arrayData = array();
            $arrayDataandcount = array();
            for($i = 0; $i<count($results); $i++){
                foreach ($presence as $key => $prec) { 
                    if($results[$i]->firebaseid == $key){
                        $object = (object) [
                            'traduccionpublic' => $results[$i]->traduccionpublic,
                            'sales' => $results[$i]->sales,
                            'videoid' => $results[$i]->id,
                            'titlevideo' => $results[$i]->titlevideo,
                            'VideoDescription' => $results[$i]->VideoDescription,
                            'business' => $results[$i]->business,
                            'direccion' => $results[$i]->direccion,
                            'pais' => $results[$i]->pais,
                            'urlvideo' => $results[$i]->urlvideo,
                            'urlimagen' => $results[$i]->urlimagen,
                            'urlvideo_width' => $results[$i]->urlvideo_width,
                            'urlvideo_height' => $results[$i]->urlvideo_height,
                            'urlimage_width' => $results[$i]->urlimage_width,
                            'urlimage_height' => $results[$i]->urlimage_height,
                            'precio' => $results[$i]->precio,
                            'idpmtype' => $results[$i]->idpmtype,
                            'serviciosId' => $results[$i]->serviciosId,
                            'traduccionservicio' => $results[$i]->traduccionservicio,
                            'firebaseid' => $results[$i]->firebaseid,
                            'timestatus' => $prec,
                            'photo' => $results[$i]->photo,
                            'logo' => $results[$i]->logo,
                            'distance' => $results[$i]->distance,
                        ];
                        array_push($arrayData, $object);
                    }   
                }
            }
            $lastobject = (object) [
                'page' => $page,
                'next' => $next,
                'results' => $arrayData,
            ];
            array_push($arrayDataandcount,$lastobject);
            return response()->json($arrayDataandcount);
            //return response()->json($arrayData);  
        }else{
            return response()->json(false);
        }
    }
    public function showallvideosbusqueda(Request $request){

        $request->validate([
            'items' => 'required',
            'page' => 'required',
            'idService' => 'required',
            'language' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $distance = 10000; // Sitios que se encuentren en un radio de 1000KM
        $idservicio = $request->idService;
        $idioma = $request->language;
        $page = $request->page;
        $items = $request->items;
        //
        $busqueda = $request->busqueda;

        // la formula
        $earthRadius = 6371;
        $objeto1 = array();
        // Los angulos para cada dirección
        $cardinalCoords = array('north' => '0', 'south' => '180', 'east' => '90', 'west' => '270');
        $rLat = deg2rad($lat);
        $rLng = deg2rad($lng);
        $rAngDist = $distance/$earthRadius;
        foreach ($cardinalCoords as $name => $angle){
            $rAngle = deg2rad($angle);
            $rLatB = asin(sin($rLat) * cos($rAngDist) + cos($rLat) * sin($rAngDist) * cos($rAngle));
            $rLonB = $rLng + atan2(sin($rAngle) * sin($rAngDist) * cos($rLat), cos($rAngDist) - sin($rLat) * sin($rLatB));
            $objeto1[$name] = array('lat' => (float) rad2deg($rLatB), 
                                    'lng' => (float) rad2deg($rLonB));
        }
        $min_lat  = $objeto1['south']['lat'];
        $max_lat = $objeto1['north']['lat'];
        $min_lng = $objeto1['west']['lng'];
        $max_lng = $objeto1['east']['lng'];
        // end la formula

        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        if($idservicio == 0){
            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic,(SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,videos.precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo, IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN '.$min_lng.' AND '.$max_lng.') HAVING distance  < '.$distance.' and concat(videos.titlevideo," ",businesses.descripcion," ", videos.VideoDescription) like "%'.$busqueda.'%" ORDER BY distance ASC;')); 
        }else{ 
            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic, (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,videos.precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo, IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and serviciousuarios.serviciosId = "'.$idservicio.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN ' . $min_lng. ' AND ' . $max_lng. ') HAVING distance  < ' . $distance . ' and concat(videos.titlevideo," ",businesses.descripcion," ", videos.VideoDescription) like "%'.$busqueda.'%" ORDER BY distance ASC;'));   
        }

        if($results){
            // firebase presence
            $reference = $database->getReference('presence');
            $snapshot = $reference->getSnapshot();
            $presence = $snapshot->getValue();
            // firebase users
            $timestatus;
            $arrayData = array();
            $arrayDataandcount = array();
            for($i = 0; $i<count($results); $i++){
                foreach ($presence as $key => $prec) { 
                    if($results[$i]->firebaseid == $key){
                        if($busqueda != ''){
                            $object = (object) [
                                'traduccionpublic' => $results[$i]->traduccionpublic,
                                'sales' => $results[$i]->sales,
                                'videoid' => $results[$i]->id,
                                'titlevideo' => $results[$i]->titlevideo,
                                'VideoDescription' => $results[$i]->VideoDescription,
                                'business' => $results[$i]->business,
                                'direccion' => $results[$i]->direccion,
                                'pais' => $results[$i]->pais,
                                'urlvideo' => $results[$i]->urlvideo,
                                'urlimagen' => $results[$i]->urlimagen,
                                'urlvideo_width' => $results[$i]->urlvideo_width,
                                'urlvideo_height' => $results[$i]->urlvideo_height,
                                'urlimage_width' => $results[$i]->urlimage_width,
                                'urlimage_height' => $results[$i]->urlimage_height,
                                'precio' => $results[$i]->precio,
                                'idpmtype' => $results[$i]->idpmtype,
                                'serviciosId' => $results[$i]->serviciosId,
                                'traduccionservicio' => $results[$i]->traduccionservicio,
                                'firebaseid' => $results[$i]->firebaseid,
                                'timestatus' => $prec,
                                'photo' => $results[$i]->photo,
                                'logo' => $results[$i]->logo,
                                'distance' => $results[$i]->distance,
                            ];
                            array_push($arrayData, $object);
                        }else{

                        }
                    }   
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
                $next = 'https://apolomultimedia-server4.info/allvideosbusqueda?items='.$items.'&page='.$nextpage.'&idService='.$idservicio.'&language='.$idioma.'&lat='.$lat.'&lng='.$lng.'&busqueda='.$busqueda;
            } 
            $lastobject = (object) [
                'page' => $page,
                'next' => $next,
                'results' => $arrayDataPaginado,
            ];
            array_push($arrayDataandcount,$lastobject);
            return response()->json($arrayDataandcount);
            //return response()->json($arrayData);  
        }else{
            return response()->json(false);
        }
    }

    public function crearvideovimeo(Request $request){
        ini_set('max_execution_time', 300);
        ini_set('default_socket_timeout', 300);
        $request->validate([
            'rutavimeo' => 'required',
            'titlevideo' => 'required', 
            'id_firebase' => 'required', 
            'VideoDescription' => 'required', 
        ]);
        $rutavimeo = $request->rutavimeo;
        $titlevideo = $request->titlevideo;
        $id_firebase = $request->id_firebase;
        $VideoDescription = $request->VideoDescription;

        // vimeo conexion
        $client = new Vimeo("775c22cdd9fc4659ce5e3d8b60983ea494d5f651", "EvxF5vcxCLcmYDoTdY14Je1WuDG7fVolfehkXEOi9ZtkWr1NXcrCwmdlrOSYj6uwKwkFvZGHRXmHW8NzD8ub5wNItDpoQBbs062//5f+8FNWUnXU2sxrOJrdjMUpqJ9a", "9a57070414710af364956455ef45bd7f");

        $responseID = $client->request(
            '/me/videos',
            [
                'name'=>$titlevideo,
                'privacy' => [
                    'view' => 'unlisted'
                ],
                'upload' => [
                    'approach' => 'pull',
                    'link' => $rutavimeo,
                ],
            ],
            'POST'
        );

        $idvimeo = $responseID['body']['uri'];
        $despertar = false;
        $newlink = '';

        do {
            sleep(10);
            $response = $client->request('/me'.$idvimeo, array(), 'GET');
            if($response['body']['duration'] != 0){

                foreach ($response['body']['files'] as $keyfile => $file) {
                
                    if($response['body']['files'][$keyfile]['quality'] != 'hls'){
                        if($response['body']['files'][$keyfile]['height'] == 960){
                            if($response['body']['files'][$keyfile]['link'] != ''){
                                $despertar = true;
                                $responsepictures = $client->request($response['body']['uri'].'/pictures', array('time' => '0'), 'POST');
                                $newlink = $responsepictures['body']['sizes'][0]['link'];
                            }else{
                                $despertar = false;
                            }
                        }else if($response['body']['files'][$keyfile]['height'] == 640){
                            if($response['body']['files'][$keyfile]['link'] != ''){
                                $despertar = true;
                                $responsepictures = $client->request($response['body']['uri'].'/pictures', array('time' => '0'), 'POST');
                                $newlink = $responsepictures['body']['sizes'][0]['link'];
                            }else{
                                $despertar = false;
                            }
                        }
                    }


                }  
            }else{
                $despertar = false;
            }    

        }while ($despertar == false);

        $data = $response['body']['name'];
        $duration = $response['body']['duration'];

        $arrayVimeo = array();
        $name = '';
        $link = '';
        $link2 = '';
        $link3 = '';
        $link3_1 = '';
        $size = '';
        // new data
        $width = '';
        $quality = '';
        $height = '';
        


        $myheight = 0;
        $mywidth = 0;
        $mylink2 = '';
        $mysize = '';


        $inicializarcontador = 0;

        $name = $response['body']['name'];
        $link = $response['body']['link'];

        do {
            foreach ($response['body']['files'] as $keyfile => $file) {
                $quality = $response['body']['files'][$keyfile]['quality'];
                if($quality == 'sd'){
                    $height = $response['body']['files'][$keyfile]['height'];
                    if($height == 960){
                        $myheight = $response['body']['files'][$keyfile]['height'];
                        $mywidth = $response['body']['files'][$keyfile]['width'];
                        $mylink2 = $response['body']['files'][$keyfile]['link'];
                        $mysize = round(($response['body']['files'][$keyfile]['size']/1024/1024), 2).'M';
                        $inicializarcontador = 4;
                    }else{
                        if($height == 640){
                            if($height < $myheight){
                                $height = $myheight;
                                $width = $mywidth;
                                $link2 = $mylink2;
                                $size = $mysize;
                                $inicializarcontador = 4;
                            }else{
                                $myheight = $response['body']['files'][$keyfile]['height'];
                                $mywidth = $response['body']['files'][$keyfile]['width'];
                                $mylink2 = $response['body']['files'][$keyfile]['link'];
                                $mysize = round(($response['body']['files'][$keyfile]['size']/1024/1024), 2).'M';
                                $inicializarcontador++;
                                sleep(10);
                            }
                            
                        } 
                    }
                }
            }
        }while ($inicializarcontador < 4);

        
        foreach ($response['body']['pictures']['sizes'] as $keypic => $pic) {
            if($response['body']['pictures']['sizes'][$keypic]['width'] == 1280 and $response['body']['pictures']['sizes'][$keypic]['height'] == 720){
                $link3 = $response['body']['pictures']['sizes'][$keypic]['link'];
                //$linksplit = explode('_',$link3);
                $linksplit = explode('_',$newlink);
                $link3_1 = $linksplit[0].'.jpg';
            }
        }
        $objectvimeo = (object) [
            'name' => $name,
            'link' => $link,
            'urlvideo' => $mylink2,
            'size' => $mysize,
            'videosize' => $duration,
            'urlimagen' => $link3_1,
            'width' => $mywidth,
            'height' => $myheight,
        ];
        array_push($arrayVimeo, $objectvimeo);  

        if(count($arrayVimeo) == 1){
            $name = $arrayVimeo[0]->name;
            $link = $arrayVimeo[0]->link;
            $urlvideo = $arrayVimeo[0]->urlvideo;
            $size = $arrayVimeo[0]->size;
            $videosize = $arrayVimeo[0]->videosize;
            $urlimagen = $arrayVimeo[0]->urlimagen;
            $width = $arrayVimeo[0]->width;
            $height = $arrayVimeo[0]->height;
            
            $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
            if(count($userId) != 0){
                $results = DB::select( DB::raw('INSERT INTO videos (id, userId, titlevideo,VideoDescription, precio, urlvideo,urlimagen,urlvideo_width,urlvideo_height,urlimage_width,urlimage_height,idpmtype,id_product_stripe,public,videosize,created_at, updated_at) VALUES (NULL, "'.$userId[0]->userId.'","'.$titlevideo.'","'.$VideoDescription.'",NULL,"'.$urlvideo.'","'.$urlimagen.'","'.$width.'","'.$height.'","'.$width.'","'.$height.'",1,NULL,22,"'.$videosize.'",now(), now());') );
                $idvideo = DB::getPdo()->lastInsertId();
                return response()->json([
                    'idvideo' => $idvideo,
                  ]);
            }else{
                return response()->json(false);
            }
        }   
    }

    public function crearvideovimeo2(Request $request){
        $request->validate([
            'rutavimeo' => 'required',
            'titlevideo' => 'required', 
            'id_firebase' => 'required', 
            'VideoDescription' => 'required', 
        ]);
        $rutavimeo = $request->rutavimeo;
        $titlevideo = $request->titlevideo;
        $id_firebase = $request->id_firebase;
        $VideoDescription = $request->VideoDescription;
        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
            if(count($userId) != 0){
                    return response()->json($userId[0]->userId);
            }else{
                return response()->json(false);
            }  
    }
}
