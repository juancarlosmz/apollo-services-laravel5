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
use App\Config;
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

            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic,(SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,(SELECT optionvaluemix.precio FROM optionvaluemix WHERE idvideo=videos.id ORDER BY optionvaluemix.precio ASC LIMIT 1) as precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo , IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance,videos.videoweight,videos.videosize FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and videos.userId = "'.$userId[0]->userId.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN '.$min_lng.' AND '.$max_lng.') HAVING distance  < '.$distance.' ORDER BY distance ASC LIMIT '.$myitems.' OFFSET '.$pageskip.';') );   

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
                            $preciooficial = "0.00";
                            if($results[$i]->precio != null){
                                $preciooficial = $results[$i]->precio;
                            }
                            $videoweightoficial = "10260237";
                            if($results[$i]->videoweight != null){
                                $videoweightoficial = $results[$i]->videoweight;
                            }

                            $videosizeoficial = "44";
                            if($results[$i]->videosize != null){
                                $videosizeoficial = $results[$i]->videosize;
                            }
                            
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
                                'precio' => $preciooficial,
                                'idpmtype' => $results[$i]->idpmtype,
                                'serviciosId' => $results[$i]->serviciosId,
                                'traduccionservicio' => $results[$i]->traduccionservicio,
                                'firebaseid' => $results[$i]->firebaseid,
                                'timestatus' => $prec,
                                'photo' => $results[$i]->photo,
                                'logo' => $results[$i]->logo,
                                'distance' => $results[$i]->distance,
                                'videoweight' => $videoweightoficial,
                                'videosize' => $videosizeoficial,
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

    public function shownewduplicado(Request $request){

        $request->validate([
            'items' => 'required',
            'page' => 'required',
            'idvideo' => 'required',
            'language' => 'required',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $distance = 10000; // Sitios que se encuentren en un radio de 1000KM
        $idvideo = $request->idvideo;
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


        $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic,(SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,(SELECT optionvaluemix.precio FROM optionvaluemix WHERE idvideo=videos.id ORDER BY optionvaluemix.precio ASC LIMIT 1) as precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo , IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance,videos.videoweight,videos.videosize FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE videos.public = 21 and videos.id = "'.$idvideo.'" and idiomas.id = "'.$idioma.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN '.$min_lng.' AND '.$max_lng.') HAVING distance  < '.$distance.' ORDER BY distance ASC LIMIT '.$myitems.' OFFSET '.$pageskip.';') );     

        //return response()->json($results);

        $nextpage = $page+1;
        // next - previous
        $next = '';
        if(count($results) > $items){
            $next = 'https://apolomultimedia-server4.info/videosbyuser?items='.$items.'&page='.$nextpage.'&idvideo='.$idvideo.'&language='.$idioma.'&lat='.$lat.'&lng='.$lng;
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
                        $videoweightoficial = "10260237";
                        if($results[$i]->videoweight != null){
                            $videoweightoficial = $results[$i]->videoweight;
                        }

                        $videosizeoficial = "44";
                        if($results[$i]->videosize != null){
                            $videosizeoficial = $results[$i]->videosize;
                        }
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
                            'videoweight' => $videoweightoficial,
                            'videosize' => $videosizeoficial,
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
            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = videos.public and idiomas.id = "'.$idioma.'") as traduccionpublic,videos.id,videos.titlevideo,videos.VideoDescription,videos.urlimagen,(SELECT optionvaluemix.precio FROM optionvaluemix WHERE idvideo=videos.id ORDER BY optionvaluemix.precio ASC LIMIT 1) as precio,videos.userId, usuarios.id_firebase as firebaseid,business_isocurrency.iso_currency FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userID = usuarios.id INNER JOIN business_isocurrency ON business_isocurrency.idbusiness = businesses.id WHERE videos.userId = "'.$userId[0]->userId.'" ORDER BY videos.id DESC, videos.public DESC LIMIT '.$myitems.' OFFSET '.$pageskip.';') );

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

                        $videomodelo = DB::select( DB::raw('select videomodelo.idmodelo FROM videomodelo WHERE videomodelo.idvideo = "'.$results[$i]->id.'";') );
                        
                        $resultsreview = DB::select( DB::raw('SELECT status_video FROM reviewvideo WHERE reviewvideo.idvideo= "'.$results[$i]->id.'" and status_video = 2;'));

                        $urlimagen = '';

                        if(count($videomodelo) != 0){
                            $idmodelo = $videomodelo[0]->idmodelo;
                        }else{
                            $idmodelo = null;
                        }

                        if(count($resultsreview) != 0){
                            $urlimagen = 'https://firebasestorage.googleapis.com/v0/b/expertify-3b3d4.appspot.com/o/publicassets%2Fdeleted_video.png?alt=media';
                        }else{
                            $urlimagen = $results[$i]->urlimagen;
                        }
                        $preciooficial = "0.00";
                        if($results[$i]->precio != null){
                            $preciooficial = $results[$i]->precio;
                        }
                        $object = (object) [
                            'traduccionpublic' => $results[$i]->traduccionpublic,
                            'videoid' => $results[$i]->id,
                            'titlevideo' => $results[$i]->titlevideo,
                            'VideoDescription' => $results[$i]->VideoDescription,
                            'urlimagen' => $urlimagen,
                            'precio' => $preciooficial,
                            'idmodelo' => $idmodelo,
                            'iso_currency' => $results[$i]->iso_currency,
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
        ]);
        $id_firebase = $request->id_firebase;
        $idioma = $request->language;
        $page = $request->page;
        $items = $request->items;
        //
        $busqueda = $request->busqueda;
        //paginacion
        $pageskip = intval($page) - 1;
        $items = intval($items);
        $myitems = $items + 1;
        $pageskip = ($pageskip) * $items;
        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if($userId){
            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = videos.public and idiomas.id = "'.$idioma.'") as traduccionpublic, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlimagen,(SELECT optionvaluemix.precio FROM optionvaluemix WHERE idvideo=videos.id ORDER BY optionvaluemix.precio ASC LIMIT 1) as precio,videos.userId,business_isocurrency.iso_currency FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userID = usuarios.id INNER JOIN business_isocurrency ON business_isocurrency.idbusiness = businesses.id WHERE videos.userId = "'.$userId[0]->userId.'" and concat(videos.titlevideo) like "%'.$busqueda.'%" ORDER BY videos.id DESC, videos.public DESC ;' ));     
            if($results){
                $arrayData = array();
                $arrayDataandcount = array();
                for($i = 0; $i<count($results); $i++){
                    if($busqueda != ''){
                        $videomodelo = DB::select( DB::raw('select videomodelo.idmodelo FROM videomodelo WHERE videomodelo.idvideo = "'.$results[$i]->id.'";') );
                        $resultsreview = DB::select( DB::raw('SELECT status_video FROM reviewvideo WHERE reviewvideo.idvideo= "'.$results[$i]->id.'" and status_video = 2;'));
                        $urlimagen = ''; 
                        if(count($videomodelo) != 0){
                            $idmodelo = $videomodelo[0]->idmodelo;
                        }else{
                            $idmodelo = null;
                        }
                        if(count($resultsreview) != 0){
                            $urlimagen = 'https://firebasestorage.googleapis.com/v0/b/expertify-3b3d4.appspot.com/o/publicassets%2Fdeleted_video.png?alt=media';
                        }else{
                            $urlimagen = $results[$i]->urlimagen;
                        }
                        $preciooficial = "0.00";
                        if($results[$i]->precio != null){
                            $preciooficial = $results[$i]->precio;
                        }
                        $object = (object) [
                            'traduccionpublic' => $results[$i]->traduccionpublic,
                            'videoid' => $results[$i]->id,
                            'titlevideo' => $results[$i]->titlevideo,
                            'VideoDescription' => $results[$i]->VideoDescription,
                            'urlimagen' => $urlimagen,
                            'precio' => $preciooficial,
                            'idmodelo' => $idmodelo,
                            'iso_currency' => $results[$i]->iso_currency,
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
                    $next = 'https://apolomultimedia-server4.info/videosbyuseradminbusqueda?items='.$items.'&page='.$nextpage.'&id_firebase='.$id_firebase.'&language='.$idioma.'&busqueda='.$busqueda;
                } 
                $lastobject = (object) [
                    'page' => $page,
                    'next' => $next,
                    'results' => $arrayDataPaginado,
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

    public function allvideosadmin(Request $request){

        $request->validate([
            'items' => 'required',
            'page' => 'required',
            'language' => 'required',
        ]);

        $idioma = $request->language;
        $page = $request->page;
        $items = $request->items;

        //paginacion
        $pageskip = intval($page) - 1;
        $items = intval($items);

        //$items = $items + 1;
        $myitems = $items + 1;
        $pageskip = ($pageskip) * $items;

        $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = videos.public and idiomas.id = "'.$idioma.'") as traduccionpublic,videos.id,videos.titlevideo,videos.VideoDescription,videos.urlimagen,videos.precio,videos.userId, usuarios.id_firebase as firebaseid,usuarios.id as user_id,usuarios.name,usuarios.photo, businesses.descripcion as businessesname, businesses.logo, reviewvideo.status_video FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN reviewvideo ON reviewvideo.idvideo = videos.id ORDER BY videos.id DESC, videos.public DESC LIMIT '.$myitems.' OFFSET '.$pageskip.';') );

        $nextpage = $page+1;
        $previouspage = $page-1;
        // next - previous
        $next = '';
        $previous = '';
        if(count($results) > $items){
            $next = 'https://apolomultimedia-server4.info/allvideosadmin?items='.$items.'&page='.$nextpage.'&language='.$idioma;
        } 
        if($previouspage != 0){
            $previous = 'https://apolomultimedia-server4.info/allvideosadmin?items='.$items.'&page='.$previouspage.'&language='.$idioma;
        }


        unset($results[$items]); 
        
        if($results){
            $arrayData = array();
            $arrayDataandcount = array();
            for($i = 0; $i<count($results); $i++){ 

                    $videomodelo = DB::select( DB::raw('select videomodelo.idmodelo FROM videomodelo WHERE videomodelo.idvideo = "'.$results[$i]->id.'";') );
                    $resultsreview = DB::select( DB::raw('SELECT status_video FROM reviewvideo WHERE reviewvideo.idvideo= "'.$results[$i]->id.'" and status_video = 2;'));
                    $urlimagen = ''; 
                    if(count($videomodelo) != 0){
                        $idmodelo = $videomodelo[0]->idmodelo;
                    }else{
                        $idmodelo = null;
                    }
                    if(count($resultsreview) != 0){
                        $urlimagen = 'https://firebasestorage.googleapis.com/v0/b/expertify-3b3d4.appspot.com/o/publicassets%2Fdeleted_video.png?alt=media';
                    }else{
                        $urlimagen = $results[$i]->urlimagen;
                    }
                    $object = (object) [
                        'traduccionpublic' => $results[$i]->traduccionpublic,
                        'firebaseid' => $results[$i]->firebaseid,
                        'user_id' => $results[$i]->user_id,
                        'name' => $results[$i]->name,
                        'photo' => $results[$i]->photo,
                        'businessesname' => $results[$i]->businessesname,
                        'logo' => $results[$i]->logo,
                        'videoid' => $results[$i]->id,
                        'revision' => $results[$i]->status_video,
                        'titlevideo' => $results[$i]->titlevideo,
                        'VideoDescription' => $results[$i]->VideoDescription,
                        'urlimagen' => $urlimagen,
                        'precio' => $results[$i]->precio,
                        'idmodelo' => $idmodelo,
                    ];
                    array_push($arrayData, $object);

            }
            $lastobject = (object) [
                'page' => $page,
                'next' => $next,
                'previous' => $previous,
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

    public function showordenespagadas(Request $request){

        $request->validate([
            'id_firebase' => 'required',
            'language' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'id_firebase_visitante' => 'required',
        ]);

        //return response()->json($arrayData);

        $lat = $request->lat;
        $lng = $request->lng;
        $distance = 10000; // Sitios que se encuentren en un radio de 1000KM
        $id_firebase = $request->id_firebase;
        $id_firebase_visitante = $request->id_firebase_visitante;
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
        $userIdvisitante = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase_visitante)
                ->get();        
        if($userId){

            $results1 = DB::select( DB::raw('select DISTINCT (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales,businesses.id as idnegocio,businesses.descripcion as business,businesses.direccion,businesses.pais,businesses.ciudad,businesses.lat,businesses.lng,businesses.Zip,businesses.details,serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo, businesses.logo FROM businesses INNER JOIN usuarios ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE  idiomas.id = "'.$idioma.'" and businesses.userId = "'.$userId[0]->userId.'" ') ); 

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
            if(count($results2) == 0){
                $distanceoficial = 0;
            }else{
                $distanceoficial = $results2[0]->distance;
            }
            //return response($lat.'**'.$lng .'**'.$userId[0]->userId.'**'.$min_lat.'**'.$max_lat.'**'.$min_lng.'**'.$max_lng);
            if($results1){

                $inserttracking = DB::select( DB::raw('INSERT INTO tracking (id, idusuario, idvideo, idnegocio, idorden, idpago ,created_at, updated_at) VALUES (NULL, "'.$userIdvisitante[0]->userId.'",0,"'.$results1[0]->idnegocio.'",0,0,now(), now());') );

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
        $client = new Vimeo(Config::VimeoConfig()['client_id'],Config::VimeoConfig()['client_secret'],Config::VimeoConfig()['access_token']);
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
        $client = new Vimeo(Config::VimeoConfig()['client_id'],Config::VimeoConfig()['client_secret'],Config::VimeoConfig()['access_token']);
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
                        //$mysize = round(($data[$keyvim]['files'][$keyfile]['size']/1024/1024), 2).'M';
                        $mysize = $data[$keyvim]['files'][$keyfile]['size'];
                    }else{
                        
                        if($myheight < $height){
                            $myheight = $data[$keyvim]['files'][$keyfile]['height'];
                            $mywidth = $data[$keyvim]['files'][$keyfile]['width'];
                            $mylink2 = $data[$keyvim]['files'][$keyfile]['link'];
                            //$mysize = round(($data[$keyvim]['files'][$keyfile]['size']/1024/1024), 2).'M';
                            $mysize = $data[$keyvim]['files'][$keyfile]['size'];
                        }
                        /*
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
                            
                        } */
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
        $client = new Vimeo(Config::VimeoConfig()['client_id'],Config::VimeoConfig()['client_secret'],Config::VimeoConfig()['access_token']);
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
        $client = new Vimeo(Config::VimeoConfig()['client_id'],Config::VimeoConfig()['client_secret'],Config::VimeoConfig()['access_token']);
        
        $responsepictures = $client->request('/videos/'.$video_id.'/pictures', array('time' => '0'), 'POST');
        $newlink = $responsepictures['body']['sizes'][0]['link'];
        return response()->json($newlink);
       
       

    }
    public function show4(){
        //$client = new Vimeo("{client_id}", "{client_secret}", "{access_token}");
        $client = new Vimeo(Config::VimeoConfig()['client_id'],Config::VimeoConfig()['client_secret'],Config::VimeoConfig()['access_token']);
        $response = $client->request('/me/videos', array(), 'GET');
        $data = $response['body']['data'];
        return response()->json($data);
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

    public function showallvideosonlytest(){
        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();


        // firebase presence by one
        $idfirebaseprec = '07c0VlrkudblBlTzbSFCCJpPer72';
        //$reference = $database->getReference('presence/'.$idfirebaseprec);
        $reference = $database->getReference('presence');
        $snapshot = $reference->getSnapshot();
        $presence = $snapshot->getValue();
        // firebase users
        return response()->json($presence);
    }


    public function showallvideosL1L2(Request $request){

        $request->validate([
            'items' => 'required',
            'page' => 'required',
            'idService' => 'required',
            'language' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'sort' => 'required',
            'id_firebase_visitante' => 'required',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;
        $distance = 10000; // Sitios que se encuentren en un radio de 1000KM
        $idservicio = $request->idService;
        $idioma = $request->language;
        $page = $request->page;
        $items = $request->items;
        //Buscar por Location, like, tag
        $sort = $request->sort;
        $id_firebase_visitante = $request->id_firebase_visitante;

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
        //
        $userIdvisitante = DB::table('usuarios')
            ->select('usuarios.id as userId')
            ->where('usuarios.id_firebase', $id_firebase_visitante)
            ->get();
        //
        $myitems = $items + 1;
        $pageskip = ($pageskip) * $items;
        if($idservicio == 0){
            $results = DB::select( DB::raw('SELECT (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height, (SELECT optionvaluemix.precio FROM optionvaluemix WHERE idvideo=videos.id ORDER BY optionvaluemix.precio ASC LIMIT 1) as precio ,videos.userId,videos.idpmtype, businesses.descripcion as business,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo, IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance, videos.videoweight, videos.videosize,business_isocurrency.iso_currency, idvideo, tiempototalvideo, idtag as idtagvideo , (SELECT idtag FROM usuariotags WHERE idtag=idtagvideo and idusuario="'.$userIdvisitante[0]->userId.'") as idtagusuario,(SELECT tiempototalusuariotags FROM usuariotags WHERE idtag=idtagvideo and idusuario="'.$userIdvisitante[0]->userId.'") as tiempototalusuariotags FROM videotagstotal INNER JOIN videos ON videos.id = videotagstotal.idvideo INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId INNER JOIN business_isocurrency ON business_isocurrency.idbusiness = businesses.id WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" GROUP by idvideo ORDER by tiempototalusuariotags DESC, tiempototalvideo DESC LIMIT '.$myitems.' OFFSET '.$pageskip.';')); 
        }else{ 
            $results = DB::select( DB::raw('SELECT (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height, (SELECT optionvaluemix.precio FROM optionvaluemix WHERE idvideo=videos.id ORDER BY optionvaluemix.precio ASC LIMIT 1) as precio ,videos.userId,videos.idpmtype, businesses.descripcion as business,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo, IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance, videos.videoweight, videos.videosize,business_isocurrency.iso_currency, idvideo, tiempototalvideo, idtag as idtagvideo , (SELECT idtag FROM usuariotags WHERE idtag=idtagvideo and idusuario="'.$userIdvisitante[0]->userId.'") as idtagusuario,(SELECT tiempototalusuariotags FROM usuariotags WHERE idtag=idtagvideo and idusuario="'.$userIdvisitante[0]->userId.'") as tiempototalusuariotags FROM videotagstotal INNER JOIN videos ON videos.id = videotagstotal.idvideo INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId INNER JOIN business_isocurrency ON business_isocurrency.idbusiness = businesses.id WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and serviciousuarios.serviciosId = "'.$idservicio.'" GROUP by idvideo ORDER by tiempototalusuariotags DESC, tiempototalvideo DESC LIMIT '.$myitems.' OFFSET '.$pageskip.';'));
        }

        //  
        //return response()->json($results);

        $nextpage = $page+1;
        $previouspage = $page-1;
        // next - previous
        $next = '';
        $previous = '';
        if(count($results) > $items){
            $next = 'https://apolomultimedia-server4.info/allvideos?items='.$items.'&page='.$nextpage.'&idService='.$idservicio.'&language='.$idioma.'&lat='.$lat.'&lng='.$lng;
        } 
        if($previouspage != 0){
            $previous = 'https://apolomultimedia-server4.info/allvideos?items='.$items.'&page='.$previouspage.'&idService='.$idservicio.'&language='.$idioma.'&lat='.$lat.'&lng='.$lng;
        }
        unset($results[$items]); 

        if($results){
            
            $arrayData = array();
            $arrayDataandcount = array();
            $timestatus = '1577836800';
            for($i = 0; $i<count($results); $i++){

                $preciooficial = "0.00";
                if($results[$i]->precio != null){
                    $preciooficial = $results[$i]->precio;
                }
                $videoweightoficial = "";
                if($results[$i]->videoweight != null){
                    $videoweightoficial = $results[$i]->videoweight;
                }

                $videosizeoficial = "";
                if($results[$i]->videosize != null){
                    $videosizeoficial = $results[$i]->videosize;
                }

                $traertags = DB::select( DB::raw('SELECT tags.id,tags.tag FROM videotags INNER JOIN tags ON tags.id = videotags.idtag WHERE videotags.idvideo = "'.$results[$i]->id.'";'));

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
                    'precio' => $preciooficial,
                    'idpmtype' => $results[$i]->idpmtype,
                    'serviciosId' => $results[$i]->serviciosId,
                    'traduccionservicio' => $results[$i]->traduccionservicio,
                    'firebaseid' => $results[$i]->firebaseid,
                    'timestatus' => $timestatus,
                    'photo' => $results[$i]->photo,
                    'logo' => $results[$i]->logo,
                    'distance' => $results[$i]->distance,
                    'videoweight' => $videoweightoficial,
                    'videosize' => $videosizeoficial,
                    'iso_currency' => $results[$i]->iso_currency,
                    'tiempototalvideo' => $results[$i]->tiempototalvideo,
                    'idtagvideo' => $results[$i]->idtagvideo,
                    'idtagusuario' => $results[$i]->idtagusuario,
                    'tiempototalusuariotags' => $results[$i]->tiempototalusuariotags,
                    'tags' => $traertags,
                ];
                array_push($arrayData, $object);


                /*
                foreach ($presence as $key => $prec) { 
                    $banderita = false;
                    if($results[$i]->firebaseid == $key ){
                        $prec = $prec;
                        $banderita = true;
                    }else{
                        if(strpos($results[$i]->firebaseid , "-") == true){
                            $prec = '1577836800';
                            $banderita = true;
                        }else{
                            $banderita = false;
                        }
                    }
                    if($banderita == true){
                        $preciooficial = "0.00";
                        if($results[$i]->precio != null){
                            $preciooficial = $results[$i]->precio;
                        }
                        $videoweightoficial = "";
                        if($results[$i]->videoweight != null){
                            $videoweightoficial = $results[$i]->videoweight;
                        }

                        $videosizeoficial = "";
                        if($results[$i]->videosize != null){
                            $videosizeoficial = $results[$i]->videosize;
                        }


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
                            'precio' => $preciooficial,
                            'idpmtype' => $results[$i]->idpmtype,
                            'serviciosId' => $results[$i]->serviciosId,
                            'traduccionservicio' => $results[$i]->traduccionservicio,
                            'firebaseid' => $results[$i]->firebaseid,
                            'timestatus' => $prec,
                            'photo' => $results[$i]->photo,
                            'logo' => $results[$i]->logo,
                            'distance' => $results[$i]->distance,
                            'videoweight' => $videoweightoficial,
                            'videosize' => $videosizeoficial,
                            'iso_currency' => $results[$i]->iso_currency,
                        ];
                        array_push($arrayData, $object);
                    }

                }
                */



            }
            $lastobject = (object) [
                'page' => $page,
                'next' => $next,
                'previous' => $previous,
                'results' => $arrayData,
            ];
            array_push($arrayDataandcount,$lastobject);
            return response()->json($arrayDataandcount);
            //return response()->json($arrayData);  
        }else{
            return response()->json(false);
        }
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
        /*
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        */
        // 
        //$items = $items + 1;
        $myitems = $items + 1;
        $pageskip = ($pageskip) * $items;
        if($idservicio == 0){
            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic,(SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height, (SELECT optionvaluemix.precio FROM optionvaluemix WHERE idvideo=videos.id ORDER BY optionvaluemix.precio ASC LIMIT 1) as precio ,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo, IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance, videos.videoweight, videos.videosize,business_isocurrency.iso_currency FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId INNER JOIN business_isocurrency ON business_isocurrency.idbusiness = businesses.id WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN '.$min_lng.' AND '.$max_lng.') HAVING distance  < '.$distance.' ORDER BY distance ASC LIMIT '.$myitems.' OFFSET '.$pageskip.';')); 
        }else{ 
            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic, (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,(SELECT optionvaluemix.precio FROM optionvaluemix WHERE idvideo=videos.id ORDER BY optionvaluemix.precio ASC LIMIT 1) as precio,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo, IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance, videos.videoweight, videos.videosize,business_isocurrency.iso_currency FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId INNER JOIN business_isocurrency ON business_isocurrency.idbusiness = businesses.id WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and serviciousuarios.serviciosId = "'.$idservicio.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN ' . $min_lng. ' AND ' . $max_lng. ') HAVING distance  < ' . $distance . ' ORDER BY distance ASC LIMIT '.$myitems.' OFFSET '.$pageskip.';'));   
        }

        //  
        //return response()->json($results);

        $nextpage = $page+1;
        $previouspage = $page-1;
        // next - previous
        $next = '';
        $previous = '';
        if(count($results) > $items){
            $next = 'https://apolomultimedia-server4.info/allvideos?items='.$items.'&page='.$nextpage.'&idService='.$idservicio.'&language='.$idioma.'&lat='.$lat.'&lng='.$lng;
        } 
        if($previouspage != 0){
            $previous = 'https://apolomultimedia-server4.info/allvideos?items='.$items.'&page='.$previouspage.'&idService='.$idservicio.'&language='.$idioma.'&lat='.$lat.'&lng='.$lng;
        }
        unset($results[$items]); 

        if($results){
            
            $arrayData = array();
            $arrayDataandcount = array();
            $timestatus = '1577836800';
            for($i = 0; $i<count($results); $i++){
/*
                if(strpos($results[$i]->firebaseid , "-") == true){
                    $timestatus = '1577836800';
                }else{
                    // firebase presence
                    $reference = $database->getReference('presence/'.$results[$i]->firebaseid);
                    $snapshot = $reference->getSnapshot();
                    $presence = $snapshot->getValue();
                    // firebase users
                    $timestatus = $presence;
                    //
                }*/


                $preciooficial = "0.00";
                if($results[$i]->precio != null){
                    $preciooficial = $results[$i]->precio;
                }
                $videoweightoficial = "";
                if($results[$i]->videoweight != null){
                    $videoweightoficial = $results[$i]->videoweight;
                }

                $videosizeoficial = "";
                if($results[$i]->videosize != null){
                    $videosizeoficial = $results[$i]->videosize;
                }


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
                    'precio' => $preciooficial,
                    'idpmtype' => $results[$i]->idpmtype,
                    'serviciosId' => $results[$i]->serviciosId,
                    'traduccionservicio' => $results[$i]->traduccionservicio,
                    'firebaseid' => $results[$i]->firebaseid,
                    'timestatus' => $timestatus,
                    'photo' => $results[$i]->photo,
                    'logo' => $results[$i]->logo,
                    'distance' => $results[$i]->distance,
                    'videoweight' => $videoweightoficial,
                    'videosize' => $videosizeoficial,
                    'iso_currency' => $results[$i]->iso_currency,
                ];
                array_push($arrayData, $object);


                /*
                foreach ($presence as $key => $prec) { 
                    $banderita = false;
                    if($results[$i]->firebaseid == $key ){
                        $prec = $prec;
                        $banderita = true;
                    }else{
                        if(strpos($results[$i]->firebaseid , "-") == true){
                            $prec = '1577836800';
                            $banderita = true;
                        }else{
                            $banderita = false;
                        }
                    }
                    if($banderita == true){
                        $preciooficial = "0.00";
                        if($results[$i]->precio != null){
                            $preciooficial = $results[$i]->precio;
                        }
                        $videoweightoficial = "";
                        if($results[$i]->videoweight != null){
                            $videoweightoficial = $results[$i]->videoweight;
                        }

                        $videosizeoficial = "";
                        if($results[$i]->videosize != null){
                            $videosizeoficial = $results[$i]->videosize;
                        }


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
                            'precio' => $preciooficial,
                            'idpmtype' => $results[$i]->idpmtype,
                            'serviciosId' => $results[$i]->serviciosId,
                            'traduccionservicio' => $results[$i]->traduccionservicio,
                            'firebaseid' => $results[$i]->firebaseid,
                            'timestatus' => $prec,
                            'photo' => $results[$i]->photo,
                            'logo' => $results[$i]->logo,
                            'distance' => $results[$i]->distance,
                            'videoweight' => $videoweightoficial,
                            'videosize' => $videosizeoficial,
                            'iso_currency' => $results[$i]->iso_currency,
                        ];
                        array_push($arrayData, $object);
                    }

                }
                */



            }
            $lastobject = (object) [
                'page' => $page,
                'next' => $next,
                'previous' => $previous,
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
        /*
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        */
        // 
        if($idservicio == 0){
            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic,(SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,(SELECT optionvaluemix.precio FROM optionvaluemix WHERE idvideo=videos.id ORDER BY optionvaluemix.precio ASC LIMIT 1) as precio ,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo, IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance, videos.videoweight,videos.videosize,business_isocurrency.iso_currency FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId INNER JOIN business_isocurrency ON business_isocurrency.idbusiness = businesses.id WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN '.$min_lng.' AND '.$max_lng.') HAVING distance  < '.$distance.' and concat(videos.titlevideo," ",businesses.descripcion," ", videos.VideoDescription) like "%'.$busqueda.'%" ORDER BY distance ASC;')); 
        }else{ 
            $results = DB::select( DB::raw('select (SELECT traduccions.descripcion from traduccions INNER JOIN valuesidiomas ON valuesidiomas.id = traduccions.validiomaId INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId WHERE valuesidiomas.id = 21 and idiomas.id = "'.$idioma.'") as traduccionpublic, (SELECT count(*) from transaccions INNER JOIN pagos ON pagos.ordenId = transaccions.ordenId WHERE transaccions.userIdvendedor = usuarios.id) as sales, videos.id,videos.titlevideo,videos.VideoDescription,videos.urlvideo,videos.urlimagen,videos.urlvideo_width,videos.urlvideo_height,videos.urlimage_width,videos.urlimage_height,(SELECT optionvaluemix.precio FROM optionvaluemix WHERE idvideo=videos.id ORDER BY optionvaluemix.precio ASC LIMIT 1) as precio ,videos.userId,videos.idpmtype,businesses.descripcion as business,businesses.direccion,businesses.pais, serviciousuarios.serviciosId,traduccions.descripcion as traduccionservicio, usuarios.id_firebase as firebaseid, usuarios.photo,businesses.logo, IF ( ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) IS NULL , 0 , ROUND((6371 * ACOS(SIN(RADIANS(businesses.lat)) * SIN(RADIANS(' . $lat . '))+ COS(RADIANS(businesses.lng - ' . $lng . '))* COS(RADIANS(businesses.lat))* COS(RADIANS(' . $lat . ')))),2) ) AS distance, videos.videoweight,videos.videosize,business_isocurrency.iso_currency FROM videos INNER JOIN usuarios ON usuarios.id = videos.userId INNER JOIN businesses ON businesses.userId = usuarios.id INNER JOIN serviciousuarios ON serviciousuarios.userId = usuarios.id INNER JOIN servicios ON servicios.id = serviciousuarios.serviciosId INNER JOIN valuesidiomas ON valuesidiomas.id = servicios.validiomaId INNER JOIN traduccions ON traduccions.validiomaId = valuesidiomas.id INNER JOIN idiomas ON idiomas.id = traduccions.idiomaId INNER JOIN business_isocurrency ON business_isocurrency.idbusiness = businesses.id WHERE videos.public = 21 and idiomas.id = "'.$idioma.'" and serviciousuarios.serviciosId = "'.$idservicio.'" and (businesses.lat BETWEEN ' . $min_lat. ' AND ' . $max_lat . ') AND (businesses.lng BETWEEN ' . $min_lng. ' AND ' . $max_lng. ') HAVING distance  < ' . $distance . ' and concat(videos.titlevideo," ",businesses.descripcion," ", videos.VideoDescription) like "%'.$busqueda.'%" ORDER BY distance ASC;'));   
        }

        if($results){
            // firebase presence
            /*
            $reference = $database->getReference('presence');
            $snapshot = $reference->getSnapshot();
            $presence = $snapshot->getValue();
            */
            // firebase users
            $arrayData = array();
            $arrayDataandcount = array();
            $timestatus = '1577836800';
            for($i = 0; $i<count($results); $i++){
                if($busqueda != ''){
                    $preciooficial = "0.00";
                    if($results[$i]->precio != null){
                        $preciooficial = $results[$i]->precio;
                    }
                    $videoweightoficial = "";
                    if($results[$i]->videoweight != null){
                        $videoweightoficial = $results[$i]->videoweight;
                    }

                    $videosizeoficial = "";
                    if($results[$i]->videosize != null){
                        $videosizeoficial = $results[$i]->videosize;
                    }
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
                        'precio' => $preciooficial,
                        'idpmtype' => $results[$i]->idpmtype,
                        'serviciosId' => $results[$i]->serviciosId,
                        'traduccionservicio' => $results[$i]->traduccionservicio,
                        'firebaseid' => $results[$i]->firebaseid,
                        'timestatus' => $timestatus,
                        'photo' => $results[$i]->photo,
                        'logo' => $results[$i]->logo,
                        'distance' => $results[$i]->distance,
                        'videoweight' => $videoweightoficial,
                        'videosize' => $videosizeoficial,
                        'iso_currency' => $results[$i]->iso_currency,
                    ];
                    array_push($arrayData, $object);
                }else{

                }
                /*
                foreach ($presence as $key => $prec) { 
                    if($results[$i]->firebaseid == $key){
                        if($busqueda != ''){
                            $preciooficial = "0.00";
                            if($results[$i]->precio != null){
                                $preciooficial = $results[$i]->precio;
                            }
                            $videoweightoficial = "";
                            if($results[$i]->videoweight != null){
                                $videoweightoficial = $results[$i]->videoweight;
                            }

                            $videosizeoficial = "";
                            if($results[$i]->videosize != null){
                                $videosizeoficial = $results[$i]->videosize;
                            }
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
                                'precio' => $preciooficial,
                                'idpmtype' => $results[$i]->idpmtype,
                                'serviciosId' => $results[$i]->serviciosId,
                                'traduccionservicio' => $results[$i]->traduccionservicio,
                                'firebaseid' => $results[$i]->firebaseid,
                                'timestatus' => $prec,
                                'photo' => $results[$i]->photo,
                                'logo' => $results[$i]->logo,
                                'distance' => $results[$i]->distance,
                                'videoweight' => $videoweightoficial,
                                'videosize' => $videosizeoficial,
                                'iso_currency' => $results[$i]->iso_currency,
                            ];
                            array_push($arrayData, $object);
                        }else{

                        }
                    }   
                }
                */
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

    public function crearvideovimeoPrimero(Request $request){
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
        ->join('businesses', 'businesses.userId', '=', 'usuarios.id')
        ->select('usuarios.id as userId', 'businesses.id as businessesid')
        ->where('usuarios.id_firebase', $id_firebase)
        ->get();
        if(count($userId) != 0){
            $results = DB::select( DB::raw('INSERT INTO videos (id, userId, titlevideo,VideoDescription, precio, urlvideo,urlimagen,urlvideo_width,urlvideo_height,urlimage_width,urlimage_height,idpmtype,id_product_stripe,public,videosize,videoweight,created_at, updated_at) VALUES (NULL, "'.$userId[0]->userId.'","'.$titlevideo.'","'.$VideoDescription.'",NULL,"","",0,0,0,0,1,NULL,22,0,0,now(), now());') );
            $idvideo = DB::getPdo()->lastInsertId();

            $insertvideotags = DB::select( DB::raw('INSERT INTO videotags (id, idvideo, idtag,created_at, updated_at) VALUES (NULL, "'.$idvideo.'",9,now(), now());') );
            
            $inserttagsvideousuario = DB::select( DB::raw('INSERT INTO tagsvideousuario (id, idusuario, idbusiness,idvideo,idtag,tiempo,created_at, updated_at) VALUES (NULL,"'.$userId[0]->userId.'","'.$userId[0]->businessesid.'", "'.$idvideo.'",9,0,now(), now());') );

            return response()->json([
                'idvideo' => $idvideo,
                'rutavimeo' => $rutavimeo,
                'titlevideo' => $titlevideo,
                'id_firebase' => $id_firebase,
                'VideoDescription' => $VideoDescription,
            ]);
        }
    }
    /*
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }*/
    
    public function crearvideovimeo(Request $request){
        ini_set('max_execution_time', 300);
        ini_set('default_socket_timeout', 300);
        $request->validate([
            'rutavimeo' => 'required',
            'titlevideo' => 'required', 
            'id_firebase' => 'required', 
            'VideoDescription' => 'required', 
            'idvideo' => 'required',
        ]);
        $rutavimeo = $request->rutavimeo;
        $titlevideo = $request->titlevideo;
        $id_firebase = $request->id_firebase;
        $VideoDescription = $request->VideoDescription;
        $idvideo = $request->idvideo;

        // vimeo conexion
        $client = new Vimeo(Config::VimeoConfig()['client_id'],Config::VimeoConfig()['client_secret'],Config::VimeoConfig()['access_token']);
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
                $despertar = true;
                $responsepictures = $client->request($response['body']['uri'].'/pictures', array('time' => '0'), 'POST');
                $newlink = $responsepictures['body']['sizes'][0]['link'];
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
        //
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
                        //$mysize = round(($response['body']['files'][$keyfile]['size']/1024/1024), 2).'M';
                        $mysize = $response['body']['files'][$keyfile]['size'];
                        $inicializarcontador = 4;
                    }else{
                        $inicializarcontador++;
                        sleep(10);
                        if($inicializarcontador == 3){
                            if($myheight < $height){
                                $myheight = $response['body']['files'][$keyfile]['height'];
                                $mywidth = $response['body']['files'][$keyfile]['width'];
                                $mylink2 = $response['body']['files'][$keyfile]['link'];
                                //$mysize = round(($response['body']['files'][$keyfile]['size']/1024/1024), 2).'M';
                                $mysize = $response['body']['files'][$keyfile]['size'];
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
            $updatevideo = DB::select( DB::raw('UPDATE videos SET urlvideo = "'.$urlvideo.'", urlimagen = "'.$urlimagen.'", urlvideo_width = "'.$width.'", urlvideo_height = "'.$height.'", urlimage_width = "'.$width.'" , urlimage_height = "'.$height.'" , videosize = "'.$videosize.'", videoweight = "'.$size.'" , updated_at = now() WHERE videos.id = "'.$idvideo.'";') );


            // eliminar firebase

            return response()->json([
                200 => 'OK',
                'message' => 'Video Actualizado',
                'urlimagen' => $urlimagen,
                'rutavimeo' => $rutavimeo
            ], 200);
        }   
    }

    public function crearvideovimeoweb(Request $request){
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
        $client = new Vimeo(Config::VimeoConfig()['client_id'],Config::VimeoConfig()['client_secret'],Config::VimeoConfig()['access_token']);

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
                $despertar = true;
                $responsepictures = $client->request($response['body']['uri'].'/pictures', array('time' => '0'), 'POST');
                $newlink = $responsepictures['body']['sizes'][0]['link'];
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
                        //$mysize = round(($response['body']['files'][$keyfile]['size']/1024/1024), 2).'M';
                        $mysize = $response['body']['files'][$keyfile]['size'];
                        $inicializarcontador = 4;
                    }else{
                        $inicializarcontador++;
                        sleep(10);
                        if($inicializarcontador == 3){
                            if($myheight < $height){
                                $myheight = $response['body']['files'][$keyfile]['height'];
                                $mywidth = $response['body']['files'][$keyfile]['width'];
                                $mylink2 = $response['body']['files'][$keyfile]['link'];
                                //$mysize = round(($response['body']['files'][$keyfile]['size']/1024/1024), 2).'M';
                                $mysize = $response['body']['files'][$keyfile]['size'];
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
                ->join('businesses', 'businesses.userId', '=', 'usuarios.id')
                ->select('usuarios.id as userId', 'businesses.id as businessesid')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();

            if(count($userId) != 0){
                $results = DB::select( DB::raw('INSERT INTO videos (id, userId, titlevideo,VideoDescription, precio, urlvideo,urlimagen,urlvideo_width,urlvideo_height,urlimage_width,urlimage_height,idpmtype,id_product_stripe,public,videosize,videoweight,created_at, updated_at) VALUES (NULL, "'.$userId[0]->userId.'","'.$titlevideo.'","'.$VideoDescription.'",NULL,"'.$urlvideo.'","'.$urlimagen.'","'.$width.'","'.$height.'","'.$width.'","'.$height.'",1,NULL,22,"'.$videosize.'","'.$size.'",now(), now());') );
                $idvideo = DB::getPdo()->lastInsertId();

                // insertando tag por default
                $insertvideotags = DB::select( DB::raw('INSERT INTO videotags (id, idvideo, idtag,created_at, updated_at) VALUES (NULL, "'.$idvideo.'",9,now(), now());') );
            
                $inserttagsvideousuario = DB::select( DB::raw('INSERT INTO tagsvideousuario (id, idusuario, idbusiness,idvideo,idtag,tiempo,created_at, updated_at) VALUES (NULL,"'.$userId[0]->userId.'","'.$userId[0]->businessesid.'", "'.$idvideo.'",9,0,now(), now());') );
                //

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

    public function eliminarvideo(Request $request){
        $request->validate([
            'idvideo' => 'required',
        ]);
        $idvideo = $request->idvideo;

        $validatestripe = DB::select( DB::raw('select videos.id_product_stripe from videos where videos.id = "'.$idvideo.'";'));

       
        if($validatestripe[0]->id_product_stripe != null){
            return response()->json([
                406 => 'Not Acceptable',
                'message' => 'No se elimina, contiene Id Stripe'
            ], 406);
        }else{
             // vimeo conexion
            $client = new Vimeo(Config::VimeoConfig()['client_id'],Config::VimeoConfig()['client_secret'],Config::VimeoConfig()['access_token']);

            $myvideoBD = DB::select( DB::raw('SELECT urlvideo FROM videos WHERE id = "'.$idvideo.'";'));

            if($myvideoBD[0]->urlvideo != ''){
                $urlexplode = explode("external/", $myvideoBD[0]->urlvideo);
                $urlexplode = explode(".", $urlexplode[1]);
                $idvimeo = $urlexplode[0];
    
                $responseDELETE = $client->request(
                    '/videos/'.$idvimeo,
                    array(),
                    'DELETE'
                );
            }else{
                $responseDELETE['status'] = 0;
            }


            if($responseDELETE['status'] == 204 || $myvideoBD[0]->urlvideo == '' || $responseDELETE['status'] == 404){

                //buscar categoria
                $buscarcategoria = DB::select( DB::raw('SELECT videos.userId,serviciousuarios.serviciosId FROM videos INNER JOIN serviciousuarios ON serviciousuarios.userId = videos.userId WHERE videos.id = "'.$idvideo.'";'));
                $serviciosId = $buscarcategoria[0]->serviciosId;
                //sumar categoria
                $sumarvideocategoria = DB::select( DB::raw('UPDATE servicios SET nvideos = nvideos-1, updated_at = now() WHERE servicios.id = "'.$serviciosId.'";') );
                
                // borrar el tag
                $eliminavideotags = DB::table('videotags')
                    ->where('videotags.idvideo',$idvideo)
                    ->delete();

                $validateoptions = DB::select( DB::raw('select distinct optionvaluemix.idvideo from optionvaluemix where optionvaluemix.idvideo = "'.$idvideo.'";'));
                $elimarvideo = false;

                if(count($validateoptions) != 0){
                    $eliminaoptionvaluemix = DB::table('optionvaluemix')
                        ->where('optionvaluemix.idvideo',$idvideo)
                        ->delete();
                    if($eliminaoptionvaluemix){
                        $eliminaoptionvalue = DB::table('optionvalue')
                        ->join('options', 'options.id', '=', 'optionvalue.idoption')
                        ->where('options.idvideo',$idvideo)
                        ->delete();
                        if($eliminaoptionvalue){
                            $eliminaoption = DB::table('options')
                                ->where('options.idvideo',$idvideo)
                                ->delete();
                            if($eliminaoption){
                                $elimarvideo = true;
                            }    
                        }
                    }
                
                }else{
                    $validaop = DB::select( DB::raw('select distinct idvideo from options inner join optionvalue ON optionvalue.idoption = options.id where idvideo = "'.$idvideo.'";'));
                    if(count($validaop) != 0){
                        $eliminaoptionvalue = DB::table('optionvalue')
                        ->join('options', 'options.id', '=', 'optionvalue.idoption')
                        ->where('options.idvideo',$idvideo)
                        ->delete();
                        if($eliminaoptionvalue){
                            $eliminaoption = DB::table('options')
                                ->where('options.idvideo',$idvideo)
                                ->delete();
                                $elimarvideo = true;
                        }
                    }else{
                        $eliminaoption = DB::table('options')
                                ->where('options.idvideo',$idvideo)
                                ->delete();
                                $elimarvideo = true;
                    } 
                }
                if($elimarvideo){
                    $videomodelo = DB::table('videomodelo')
                        ->where('idvideo',$idvideo)
                        ->delete();

                    $reviewvideo = DB::table('reviewvideo')
                        ->where('idvideo',$idvideo)
                        ->delete();

                    if($reviewvideo){
                        $todoelvideo = DB::table('videos')
                            ->where('id',$idvideo)
                            ->delete();
                        if($todoelvideo){
                            return response()->json([
                                200 => 'OK',
                                'message' => 'Video eliminado',
                                'contenid vimeo' => $responseDELETE
                            ], 200);
                        }
                    }else{
                        $todoelvideo = DB::table('videos')
                            ->where('id',$idvideo)
                            ->delete();
                        if($todoelvideo){
                            return response()->json([
                                200 => 'OK',
                                'message' => 'Video eliminado',
                                'contenid vimeo' => $responseDELETE
                            ], 200);
                        }
                    }    

                    if($videomodelo){
                        $todoelvideo = DB::table('videos')
                            ->where('id',$idvideo)
                            ->delete();
                        if($todoelvideo){
                            return response()->json([
                                200 => 'OK',
                                'message' => 'Video eliminado',
                                'contenid vimeo' => $responseDELETE
                            ], 200);
                        }
                    }else{
                        $todoelvideo = DB::table('videos')
                            ->where('id',$idvideo)
                            ->delete();
                        if($todoelvideo){
                            return response()->json([
                                200 => 'OK',
                                'message' => 'Video eliminado',
                                'contenid vimeo' => $responseDELETE
                            ], 200);
                        }
                    }   
                }else{
                    $videomodelo = DB::table('videomodelo')
                        ->where('idvideo',$idvideo)
                        ->delete();
                    $reviewvideo = DB::table('reviewvideo')
                        ->where('idvideo',$idvideo)
                        ->delete();
                    if($reviewvideo){
                        $todoelvideo = DB::table('videos')
                            ->where('id',$idvideo)
                            ->delete();
                        if($todoelvideo){
                            return response()->json([
                                200 => 'OK',
                                'message' => 'Video eliminado',
                                'contenid vimeo' => $responseDELETE
                            ], 200);
                        }else{
                            return response()->json([
                                204 => 'No Content',
                                'message' => 'Este video no existe'
                            ], 204); 
                        }
                    }else{
                        $todoelvideo = DB::table('videos')
                            ->where('id',$idvideo)
                            ->delete();
                        if($todoelvideo){
                            return response()->json([
                                200 => 'OK',
                                'message' => 'Video eliminado',
                                'contenid vimeo' => $responseDELETE
                            ], 200);
                        }else{
                            return response()->json([
                                204 => 'No Content',
                                'message' => 'Este video no existe'
                            ], 204); 
                        }
                    } 
                    if($videomodelo){
                        $todoelvideo = DB::table('videos')
                            ->where('id',$idvideo)
                            ->delete();
                        if($todoelvideo){
                            return response()->json([
                                200 => 'OK',
                                'message' => 'Video eliminado',
                                'contenid vimeo' => $responseDELETE
                            ], 200);
                        }else{
                            return response()->json([
                                204 => 'No Content',
                                'message' => 'Este video no existe'
                            ], 204); 
                        }
                    }else{
                        $todoelvideo = DB::table('videos')
                        ->where('id',$idvideo)
                        ->delete();
                        if($todoelvideo){
                            return response()->json([
                                200 => 'OK',
                                'message' => 'Video eliminado',
                                'contenid vimeo' => $responseDELETE
                            ], 200);
                        }else{
                            return response()->json([
                                204 => 'No Content',
                                'message' => 'Este video no existe'
                            ], 204); 
                        }
                    }
                }
            }else{
                return response()->json([
                    400 => 'Bad Request',
                    'message' => 'El servicio no pudo eliminar, intentalo despues',
                ], 400);
            } 
        }
    }
    // actualizar video
    public function actualizarvideo(Request $request){
        ini_set('max_execution_time', 300);
        ini_set('default_socket_timeout', 300);
        $request->validate([
            'rutavimeo' => 'required',
            'idvideo' => 'required',
        ]);
        $rutavimeo = $request->rutavimeo;
        $idvideo = $request->idvideo;
        
        // vimeo conexion
        $client = new Vimeo(Config::VimeoConfig()['client_id'],Config::VimeoConfig()['client_secret'],Config::VimeoConfig()['access_token']);

        $myvideoBD = DB::select( DB::raw('SELECT titlevideo,urlvideo FROM videos WHERE id = "'.$idvideo.'";'));

        //$updatevideopublic = DB::select( DB::raw('UPDATE videos SET public = 22, updated_at = now() WHERE videos.id = "'.$idvideo.'";') );

        if($myvideoBD[0]->urlvideo != ''){
            $urlexplode = explode("external/", $myvideoBD[0]->urlvideo);
            $urlexplode = explode(".", $urlexplode[1]);
            $idvimeo = $urlexplode[0];
            $responseDELETE = $client->request(
                '/videos/'.$idvimeo,
                array(),
                'DELETE'
            );
        }else{
            $responseDELETE['status'] = 0;
        }
        if($responseDELETE['status'] == 204 || $responseDELETE['status'] == 0){

            $reviewvideo = DB::table('reviewvideo')
            ->where('idvideo',$idvideo)
            ->delete();

            $updatevideofirst = DB::select( DB::raw('UPDATE videos SET urlvideo = "", urlimagen = "", urlvideo_width = 0, urlvideo_height = 0, urlimage_width = 0 , urlimage_height = 0 , videosize = 0, videoweight = 0  , public = 22, updated_at = now() WHERE videos.id = "'.$idvideo.'";') );

            $responseID = $client->request(
                '/me/videos',
                [
                    'name'=>$myvideoBD[0]->titlevideo,
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
                    $despertar = true;
                    $responsepictures = $client->request($response['body']['uri'].'/pictures', array('time' => '0'), 'POST');
                    $newlink = $responsepictures['body']['sizes'][0]['link'];
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
            //
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
                            //$mysize = round(($response['body']['files'][$keyfile]['size']/1024/1024), 2).'M';
                            $mysize = $response['body']['files'][$keyfile]['size'];
                            $inicializarcontador = 4;
                        }else{
                            $inicializarcontador++;
                            sleep(10);
                            if($inicializarcontador == 3){
                                if($myheight < $height){
                                    $myheight = $response['body']['files'][$keyfile]['height'];
                                    $mywidth = $response['body']['files'][$keyfile]['width'];
                                    $mylink2 = $response['body']['files'][$keyfile]['link'];
                                    //$mysize = round(($response['body']['files'][$keyfile]['size']/1024/1024), 2).'M';
                                    $mysize = $response['body']['files'][$keyfile]['size'];
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
                $updatevideo = DB::select( DB::raw('UPDATE videos SET urlvideo = "'.$urlvideo.'", urlimagen = "'.$urlimagen.'", urlvideo_width = "'.$width.'", urlvideo_height = "'.$height.'", urlimage_width = "'.$width.'" , urlimage_height = "'.$height.'" , videosize = "'.$videosize.'", videoweight = "'.$size.'" , public = 22, updated_at = now() WHERE videos.id = "'.$idvideo.'";') );
                return response()->json([
                    200 => 'OK',
                    'message' => 'Video Actualizado',
                    'urlimagen' => $urlimagen,
                    'rutavimeo' => $rutavimeo
                ], 200);
            }   

        }else{
            return response()->json([
                400 => 'Bad Request',
                'message' => 'Necesitas tener registrado un video para actualizar, intentalo despues',
            ], 400);
        }
    }
    // creando tags

    public function creartags(Request $request){
        $request->validate([
            'idvideo' => 'required',
        ]);
        $idvideo = $request->idvideo;

        $checkTag = DB::select( DB::raw('SELECT urlvideo FROM videos WHERE id = "'.$idvideo.'";'));
    }

    // buscar tags
    public function searchtags(Request $request){

        $request->validate([
            'searchtags' => 'required',
        ]);
        $searchtags = $request->searchtags;

        $result = DB::select( DB::raw('SELECT tags.id,tags.tag,tags.tiempototal FROM tags WHERE tags.tag LIKE "%'.$searchtags.'%" ORDER BY tags.tiempototal DESC;'));
        if($result){
            return response()->json([
                200 => 'OK',
                'response' => $result,
            ], 200);
        }else{
            return response()->json([
                200 => 'Ok',
                'response' => '',
            ], 200);
        }
    }

    // add masive tags
    public function addtags(Request $request){
        $request->validate([
            'idvideo' => 'required',
            'tags' => 'required',
        ]);
        $idvideo = $request->idvideo;
        $tags = $request->tags;

        $validavideotags = DB::select( DB::raw('SELECT * FROM videotags WHERE idvideo ="'.$idvideo.'";'));
        if($validavideotags){
            $eliminaoption = DB::table('videotags')
            ->where('idvideo',$idvideo)
            ->delete();
        }

        for($i = 0; $i<count($tags); $i++){
            $idtag = $tags[$i]['idtag'];
            if($tags[$i]['idtag'] == 0){
                $inserttags = DB::select( DB::raw('INSERT INTO tags (id, tag, tiempototal, nveces, lastid, created_at, updated_at) VALUES (NULL, "'.$tags[$i]['tag'].'",0,0,0,now(), now());'));
                $idtaglastInsert = DB::getPdo()->lastInsertId();  
                $idtag = $idtaglastInsert;
            }
            $insertvideotags = DB::select( DB::raw('INSERT INTO videotags (id, idvideo, idtag,created_at, updated_at) VALUES (NULL, "'.$idvideo.'","'.$idtag.'",now(), now());')); 
        }
        return response()->json([
            200 => 'OK'
        ], 200);
    }

    //
    public function cronjobvideotagusuario(){

        $tags = DB::select( DB::raw('SELECT MAX(id) as maxid, lastid  as ultimotag FROM tags;'));

        $ultimotaglastid = $tags[0]->ultimotag;

        // P_1: actualizar tabla tags
        
        $tagsvideousuario = DB::select( DB::raw('SELECT tagsvideousuario.idtag ,SUM(tagsvideousuario.tiempo) AS tiempototal, COUNT(tagsvideousuario.idtag) as nveces, (SELECT MAX(id) FROM tagsvideousuario LIMIT 1) AS lastid FROM tagsvideousuario WHERE tagsvideousuario.id > "'.$ultimotaglastid.'" GROUP BY tagsvideousuario.idtag ORDER BY tagsvideousuario.id ASC LIMIT 50'));

        $stringupdate1 = 'INSERT INTO tags (id,tiempototal,nveces,lastid) VALUES ';
        $stringupdate2 = '';
        $stringupdate3 = ' ON DUPLICATE KEY UPDATE id=VALUES(id), tiempototal=VALUES(tiempototal), nveces=VALUES(nveces), lastid=VALUES(lastid)';

        for($i = 0; $i<count($tagsvideousuario); $i++){

            $tablatags = DB::select( DB::raw('SELECT * FROM tags WHERE id = "'.$tagsvideousuario[$i]->idtag.'"'));

            $tiempototal = $tagsvideousuario[$i]->tiempototal + $tablatags[0]->tiempototal;
            $nveces = $tagsvideousuario[$i]->nveces + $tablatags[0]->nveces;

            if(count($tagsvideousuario)-1 == $i){
                $stringupdate2 .= "(".$tagsvideousuario[$i]->idtag.",".$tiempototal.",".$nveces.",".$tagsvideousuario[$i]->lastid.")";
            }else{
                $stringupdate2 .= "(".$tagsvideousuario[$i]->idtag.",".$tiempototal.",".$nveces.",".$tagsvideousuario[$i]->lastid."),";
            }

        }
        $consultamasiva = $stringupdate1.$stringupdate2.$stringupdate3;

        $updatetags = DB::select( DB::raw($consultamasiva));
        
        // P_2: actualizar tabla usuariotags
        
        $tagtiempousuario = DB::select( DB::raw('SELECT SUM(tiempo) as tiempototalusuariotags, idtag, idusuario,(SELECT MAX(id) FROM tagsvideousuario LIMIT 1) AS lastid FROM tagsvideousuario WHERE tagsvideousuario.id > "'.$ultimotaglastid.'" GROUP BY idtag, idusuario ORDER BY idusuario ASC,tiempototalusuariotags DESC, idtag DESC LIMIT 50'));

        $stringupdateusertag1 = 'INSERT INTO usuariotags (id,idusuario,idtag,tiempototalusuariotags,lastid) VALUES ';
        $stringupdateusertag2 = '';
        $stringupdateusertag3 = ' ON DUPLICATE KEY UPDATE id=VALUES(id), idusuario=VALUES(idusuario), idtag=VALUES(idtag), tiempototalusuariotags=VALUES(tiempototalusuariotags), lastid=VALUES(lastid)';

        for($j = 0; $j<count($tagtiempousuario); $j++){

            $id_tagsvideousuario = 'null';

            $tablausuariotags = DB::select( DB::raw('SELECT id,tiempototalusuariotags FROM usuariotags WHERE idusuario = "'.$tagtiempousuario[$j]->idusuario.'" and idtag = "'.$tagtiempousuario[$j]->idtag.'" '));

            $tiempototal2 = $tagtiempousuario[$j]->tiempototalusuariotags;

            if(count($tablausuariotags) != 0){
                $id_tagsvideousuario = $tablausuariotags[0]->id;
                $tiempototal2 = $tagtiempousuario[$j]->tiempototalusuariotags + $tablausuariotags[0]->tiempototalusuariotags;
            }

            if(count($tagtiempousuario)-1 == $j){
                $stringupdateusertag2 .= "(".$id_tagsvideousuario.",".$tagtiempousuario[$j]->idusuario.",".$tagtiempousuario[$j]->idtag.",".$tiempototal2.",".$tagtiempousuario[$j]->lastid.")";
            }else{
                $stringupdateusertag2 .= "(".$id_tagsvideousuario.",".$tagtiempousuario[$j]->idusuario.",".$tagtiempousuario[$j]->idtag.",".$tiempototal2.",".$tagtiempousuario[$j]->lastid."),";
            }
            
        }

        $consultamasiva2 = $stringupdateusertag1.$stringupdateusertag2.$stringupdateusertag3;

        $updateusuariotags = DB::select( DB::raw($consultamasiva2));
        

        // P_3: actualizar tabla videotagstotal

        $tagsvideousuario2 = DB::select( DB::raw('SELECT id, idbusiness,idvideo, idtag, sum(tiempo) AS tiempototalvideo,(SELECT MAX(id) FROM tagsvideousuario LIMIT 1) AS lastid from tagsvideousuario WHERE tagsvideousuario.id > "'.$ultimotaglastid.'" GROUP by idvideo,idtag ORDER BY tagsvideousuario.idvideo ASC LIMIT 50'));

        $stringupdatevideotagstotal1 = 'INSERT INTO videotagstotal (id,idbusiness,idvideo,idtag,tiempototalvideo,lastid) VALUES ';
        $stringupdatevideotagstotal2 = '';
        $stringupdatevideotagstotal3 = ' ON DUPLICATE KEY UPDATE id=VALUES(id), idbusiness=VALUES(idbusiness), idvideo=VALUES(idvideo), idtag=VALUES(idtag), tiempototalvideo=VALUES(tiempototalvideo), lastid=VALUES(lastid)';
        
        for($k = 0; $k<count($tagsvideousuario2); $k++){

            $id_tagsvideousuario = 'null';

            $tablavideotagstotal = DB::select( DB::raw('SELECT id,tiempototalvideo FROM videotagstotal WHERE idvideo = "'.$tagsvideousuario2[$k]->idvideo.'" and idtag = "'.$tagsvideousuario2[$k]->idtag.'" '));

            $tiempototal3 = $tagsvideousuario2[$k]->tiempototalvideo;

            if(count($tablavideotagstotal) != 0){
                
                $id_tagsvideousuario = $tablavideotagstotal[0]->id;
                $tiempototal3 = $tagsvideousuario2[$k]->tiempototalvideo + $tablavideotagstotal[0]->tiempototalvideo;
            }

            if(count($tagsvideousuario2)-1 == $k){
                $stringupdatevideotagstotal2 .= "(".$id_tagsvideousuario.",".$tagsvideousuario2[$k]->idbusiness.",".$tagsvideousuario2[$k]->idvideo.",".$tagsvideousuario2[$k]->idtag.",".$tiempototal3.",".$tagsvideousuario2[$k]->lastid.")";
            }else{
                $stringupdatevideotagstotal2 .= "(".$id_tagsvideousuario.",".$tagsvideousuario2[$k]->idbusiness.",".$tagsvideousuario2[$k]->idvideo.",".$tagsvideousuario2[$k]->idtag.",".$tiempototal3.",".$tagsvideousuario2[$k]->lastid."),";
            }
            
        }

        $consultamasiva3 = $stringupdatevideotagstotal1.$stringupdatevideotagstotal2.$stringupdatevideotagstotal3;

        $updatevideotagstotal = DB::select( DB::raw($consultamasiva3));

        return response()->json([
            200 => 'OK'
        ], 200);
    }


}
