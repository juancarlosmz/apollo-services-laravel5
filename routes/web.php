<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('userfb','FirebaseController@index');

// servicios servicio
Route::get('servicio','ServicioController@index');
Route::post('servicio','ServicioController@store');
Route::put('servicio/{id}','ServicioController@update');
Route::delete('servicio/{id}','ServicioController@destroy');

// servicios tipouser
Route::get('tipouser','TipouserController@index');
Route::post('tipouser','TipouserController@store');
Route::put('tipouser/{id}','TipouserController@update');
Route::delete('tipouser/{id}','TipouserController@destroy');

// servicios usuario
Route::get('usuario','UsuarioController@index');
Route::post('usuario','UsuarioController@store');
Route::put('usuario/{id}','UsuarioController@update');
Route::delete('usuario/{id}','UsuarioController@destroy');

// servicios deposito
Route::get('deposito','DepositoController@index');
Route::post('deposito','DepositoController@store');
Route::put('deposito/{id}','DepositoController@update');
Route::delete('deposito/{id}','DepositoController@destroy');

// servicios cuenta bancaria
Route::get('cuentabancaria','CuentabancariaController@index');
Route::post('cuentabancaria','CuentabancariaController@store');
Route::put('cuentabancaria/{id}','CuentabancariaController@update');
Route::delete('cuentabancaria/{id}','CuentabancariaController@destroy');

// servicios cuenta bancaria
Route::get('pago','PagoController@index');
Route::post('pago','PagoController@store');
Route::put('pago/{id}','PagoController@update');
Route::delete('pago/{id}','PagoController@destroy');

Route::get('/', function () {
    return view('welcome');
});
