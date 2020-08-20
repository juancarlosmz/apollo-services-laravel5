<?php
namespace App\Http\Controllers;
use App\Depositoorden;
use Illuminate\Http\Request;
use App\Http\Requests\DepositoordenRequest;
use Illuminate\Support\Facades\DB;
// Firebase conect
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
class DepositoordenController extends Controller{
    public function show($id_firebase){
        // Firebaseb conect
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
            ->create();
        $database = $firebase->getDatabase();
        // 
        // varianles
        $usernameVendedor;
        $usernamecomprador;
        $data = DB::table('depositoordens')
            ->join('depositos', 'depositos.id', '=', 'depositoordens.depositoId')
            ->join('ordens', 'ordens.id', '=', 'depositoordens.ordenId')
            ->join('usuarios', 'usuarios.id', '=', 'depositos.userId')
            ->join('transaccions', 'transaccions.ordenId', '=', 'ordens.id')
            ->select('depositos.id', 'depositos.fecha_deposito', 'ordens.id', 'ordens.descripcion', 'ordens.fechaentrega', 'usuarios.id_firebase as id_firebaseVendedor', DB::raw('(SELECT usuarios.id_firebase FROM usuarios WHERE usuarios.id = transaccions.userIdcomprador) AS id_firebaseComprador'), 'ordens.total as totalOrden', 'depositos.total as totalDeposito','depositoordens.numero','depositoordens.banco')
            ->where('usuarios.id_firebase', $id_firebase)
            ->get();
        if($data){
            $arrayData = array();
            // firebase
            $reference = $database->getReference('users');
            $snapshot = $reference->getSnapshot();
            $userfb = $snapshot->getValue();
            //
            for($i = 0; $i<count($data); $i++){
               foreach ($userfb as $key => $usfb) { 
                    if($data[$i]->id_firebaseVendedor == $key){
                        $usernameVendedor = array_values($usfb)[0];  
                    }
                    if($data[$i]->id_firebaseComprador == $key){
                        $usernamecomprador = array_values($usfb)[0];   
                    }
                } 
            }
            for($i = 0; $i<count($data); $i++){
                     $object = (object) [
                         'id' => $data[$i]->id,
                         'fecha_deposito' => $data[$i]->fecha_deposito,
                         'descripcion' => $data[$i]->descripcion,
                         'fechaentrega' => $data[$i]->fechaentrega,
                         'id_firebaseVendedor' => $usernameVendedor,
                         'id_firebaseComprador' => $usernamecomprador,
                         'totalOrden' => $data[$i]->totalOrden,
                         'totalDeposito' => $data[$i]->totalDeposito,
                         'numero' => $data[$i]->numero,
                         'banco' => $data[$i]->banco
                     ];
                     array_push($arrayData, $object);
     
             }
            return response()->json($arrayData); 
        } else {
            return false;
        } 
    }
}
