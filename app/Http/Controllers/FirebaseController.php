<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
// Firebase conection
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
class FirebaseController extends Controller{
    public function index(){
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/expertify-3b3d4-firebase-adminsdk-l30jf-1e53428b24.json');
        $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri('https://expertify-3b3d4.firebaseio.com/')
        ->create();
        $database = $firebase->getDatabase();
        $reference = $database->getReference('users');
        $snapshot = $reference->getSnapshot();
        $userfb = $snapshot->getValue();

        return $userfb;

    }
}
