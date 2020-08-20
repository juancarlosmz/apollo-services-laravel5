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
                    'details' => $request->details,
                    'lat' => $request->lat,
                    'lng' => $request->lng,
                    'pais' => $request->pais,
                    'ciudad' => $request->ciudad,
                    'Zip' => $request->zip
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
                    'details' => $request->details,
                    'lat' => $request->lat,
                    'lng' => $request->lng,
                    'pais' => $request->pais,
                    'ciudad' => $request->ciudad,
                    'Zip' => $request->zip
                ]);
                return response(200);
            }   
        }
    }
    public function show($id_firebase){
        $data = DB::table('usuarios')
            ->join('businesses', 'businesses.userId', '=', 'usuarios.id')
            ->join('serviciousuarios', 'serviciousuarios.userId', '=', 'usuarios.id')
            ->select('serviciousuarios.serviciosId', 'businesses.descripcion', 'businesses.details', 'businesses.lat', 'businesses.lng', 'businesses.direccion', 'businesses.pais', 'businesses.ciudad', 'businesses.Zip as zip')
            ->where('usuarios.id_firebase', $id_firebase)
            ->get();
        if(count($data) == 0){  
            return response()->json(false);
        }else{
            return $data;
        }  
        
    }
    public function show2($id_firebase){
        $userId = DB::table('usuarios')
                ->select('usuarios.id_firebase as firebaseId','usuarios.fecha_nacimiento','usuarios.sexo','usuarios.idiomaId')
                ->where('usuarios.id_firebase', $id_firebase)
                ->get();
        if(count($userId) == 0){  
            return response()->json(false);
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
