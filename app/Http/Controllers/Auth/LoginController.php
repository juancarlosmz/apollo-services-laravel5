<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;


class LoginController extends Controller{
    use AuthenticatesUsers;
    protected $redirectTo = '/home';
    public function __construct(){
        $this->middleware('guest')->except('logout');
    }
    public function login(Request $request) {
        $respuesta = $request->json()->all();
        $user = User::where('email', $respuesta['email'])->first();
        if ($user) {
            if (Hash::check($respuesta['password'], $user->password)) {
                $response = ["email" => $user->email];
                return response()->json($user);
            } else {
                $response = ["email" => "falsepasswd"];
                return response()->json($response);
            }
        } else {
            $response = ["email" =>'falseuser'];
            return response()->json($response);
        }
    }
    public function leerUsuario(Request $request) {
        $respuesta = $request->json()->all();
        $results = DB::select( DB::raw('select * from users;'));
        return response()->json($respuesta);
    }
}
