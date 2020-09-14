<?php
namespace App\Http\Controllers;
use App\Usuario;
use App\Business;
use App\Serviciousuario;
use Illuminate\Http\Request;
use App\Http\Requests\UsuarioRequest;
use Illuminate\Support\Facades\DB;
class UsuarioController extends Controller{
    public function create(Request $request){
        $userId = DB::table('usuarios')
            ->select('usuarios.id as userId')
            ->where('usuarios.id_firebase', $request->id_firebase)
            ->get();
        if(count($userId) == 0){
            // new code
            $userphone = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.phone', $request->phone)
                ->get();
            if(count($userphone) == 0){
                $objeto = Usuario::create([
                    'phone' => $request->phone,
                    'id_firebase' => $request->id_firebase,
                    'fecha_nacimiento' => null,
                    'name' => $request->name,
                    'photo' => $request->photo,
                    'sexo' => null,
                    'idiomaId' => $request->idiomaId
                    ]);
                if($objeto){
                    return response(200);
                }
            }else{
                $objeto3 = DB::table('usuarios')
                    ->where('phone', $request->phone)
                    ->update([
                        'phone' => $request->phone,
                        'id_firebase' => $request->id_firebase,
                        'name' => $request->name,
                        'photo' => $request->photo,
                        'idiomaId' => $request->idiomaId
                ]);
                if($objeto3){
                    return response(200);
                }
            }    
            //end new code
        } else {
            $objeto2 = DB::table('usuarios')
                ->where('id_firebase', $request->id_firebase)
                ->update([
                'phone' => $request->phone,
                'name' => $request->name,
                'photo' => $request->photo,
            ]);
            if($objeto2){
                return response(200);
            }
        }     
    }
    public function create2(Request $request){
        $userId = DB::table('usuarios')
            ->select('usuarios.id as userId')
            ->where('usuarios.id_firebase', $request->id_firebase)
            ->get();    
        if($userId){
            $validaterelacion = DB::table('serviciousuarios')
                ->select('serviciousuarios.id')
                ->where('serviciousuarios.userId', $userId[0]->userId)
                ->get();
            if(count($validaterelacion) == 0){ 
                $objeto2 = Business::create([
                    'userId' => $userId[0]->userId,
                    'descripcion' => $request->nombreservicio,
                    'direccion' => $request->direccion,
                    'details' => $request->details,
                    'lat' => $request->lat,
                    'lng' => $request->lng,
                    'pais' => $request->pais,
                    'ciudad' => $request->ciudad,
                    'Zip' => $request->zip,
                    'logo' => $request->logo,
                    ]);
                $objeto3 = Serviciousuario::create([
                    'userId' => $userId[0]->userId,
                    'serviciosId' => $request->servicioId
                    ]);    
                if($objeto3){
                    return response(200);
                }
            }else{
                $objeto = DB::table('serviciousuarios')
                    ->where('userId', $userId[0]->userId)
                    ->update([
                    'serviciosId' => $request->servicioId
                ]);
                $objeto2 = DB::table('businesses')
                    ->where('userId', $userId[0]->userId)
                    ->update([
                    'descripcion' => $request->nombreservicio,
                    'direccion' => $request->direccion,
                    'details' => $request->details,
                    'lat' => $request->lat,
                    'lng' => $request->lng,
                    'pais' => $request->pais,
                    'ciudad' => $request->ciudad,
                    'Zip' => $request->zip,
                    'logo' => $request->logo,
                ]);
                return response(200);
            }   
        }
    }
    public function show($id_firebase){
        $data = DB::table('usuarios')
            ->join('businesses', 'businesses.userId', '=', 'usuarios.id')
            ->join('serviciousuarios', 'serviciousuarios.userId', '=', 'usuarios.id')
            ->select('serviciousuarios.serviciosId', 'businesses.descripcion', 'businesses.details', 'businesses.lat', 'businesses.lng', 'businesses.direccion', 'businesses.pais', 'businesses.ciudad', 'businesses.Zip', 'businesses.logo')
            ->where('usuarios.id_firebase', $id_firebase)
            ->get();
        if(count($data) == 0){  
            $empty = (object) [
                'serviciosId' => null,
                'descripcion' => null,
                'details' => null,
                'lat' => null,
                'lng' => null,
                'direccion' => null,
                'pais' => null,
                'ciudad' => null,
                'zip' => null,
                'logo' => null,
            ];
            return response()->json([$empty]);
        }else{
            $arrayData = array();
            for($i = 0; $i<count($data); $i++){
                $logo = '';
                $details = '';
                $direccion = '';
                $pais = '';
                $ciudad = '';
                $Zip = '';
                if($data[$i]->details == null){
                    $details = '';
                }else{
                    $details = $data[$i]->details;
                }
                if($data[$i]->direccion == null){
                    $direccion = '';
                }else{
                    $direccion = $data[$i]->direccion;
                }
                if($data[$i]->pais == null){
                    $pais = '';
                }else{
                    $pais = $data[$i]->pais;
                }
                if($data[$i]->ciudad == null){
                    $ciudad = '';
                }else{
                    $ciudad = $data[$i]->ciudad;
                }
                if($data[$i]->Zip == null){
                    $Zip = '';
                }else{
                    $Zip = $data[$i]->Zip;
                }
                if($data[$i]->logo == null){
                    $logo = '';
                }else{
                    $logo = $data[$i]->logo;
                }
                $object = (object) [
                    'serviciosId' => $data[$i]->serviciosId,
                    'descripcion' => $data[$i]->descripcion,
                    'details' => $details,
                    'lat' => $data[$i]->lat,
                    'lng' => $data[$i]->lng,
                    'direccion' => $direccion,
                    'pais' => $pais,
                    'ciudad' => $ciudad,
                    'zip' => $Zip,
                    'logo' => $logo,
                ];
                array_push($arrayData, $object);
            }
            return response()->json($arrayData); 
            //return $data;
        }  
        
    }
    public function show2($id_firebase,$phone){
        $userId = DB::table('usuarios')
                ->select('usuarios.id_firebase as firebaseId','usuarios.fecha_nacimiento','usuarios.sexo','usuarios.name','usuarios.photo','usuarios.idiomaId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if(count($userId) == 0){  
                $userphone = DB::table('usuarios')
                ->select('usuarios.id_firebase as firebaseId','usuarios.fecha_nacimiento','usuarios.sexo','usuarios.name','usuarios.photo','usuarios.idiomaId')
                ->where('usuarios.phone', $phone)
                ->get();
          if(count($userphone) == 0){
               return response()->json(false);
            }else{
               return $userphone;
        }
        }else{
            return $userId;
        }   
    }
    public function update(Request $request, $id_firebase){
        $objeto = DB::table('usuarios')
            ->where('id_firebase', $id_firebase)
            ->update([
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo' => $request->sexo
        ]);
        return response(200);
    }
    public function update2(Request $request, $id_firebase){
        $userId = DB::table('usuarios')
                ->select('usuarios.id as userId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if($userId){
            $objeto = DB::table('serviciousuarios')
                ->where('userId', $userId[0]->userId)
                ->update([
                'serviciosId' => $request->servicioId
            ]);
            $objeto2 = DB::table('businesses')
                ->where('userId', $userId[0]->userId)
                ->update([
                'descripcion' => $request->descripcion
            ]);
            return response(200);
        }
    }
/*
    public function destroy($id){
        $objeto = Usuario::findOrFail($id);
        $objeto->delete();
        return response()->json(true);
    }
    */
}
