<?php
Route::get('userfb','FirebaseController@index');

// servicios servicio
Route::get('servicio/{idioma}','ServicioController@show');
Route::get('serviciovideo/{idioma}','ServicioController@show2');

// servicios usuario
Route::get('usuarioservicio/{id_firebase}','UsuarioController@show');
Route::get('listarusuario/{id_firebase}','UsuarioController@show2');
Route::post('usuario','UsuarioController@create');
Route::post('usuarioservicio','UsuarioController@create2');
Route::put('usuario/{id_firebase}','UsuarioController@update');
/*Route::put('usuarioservicio/{id_firebase}','UsuarioController@update2');*/

// servicios cuenta bancaria
Route::get('cuentabancaria/{id_firebase}','CuentabancariaController@show');
Route::post('cuentabancaria/{id_firebase}','CuentabancariaController@create');
Route::put('cuentabancaria/{id_firebase}','CuentabancariaController@update');

// servicios pago
Route::post('pago','PagoController@create');
Route::post('transaccion','PagoController@create2');
Route::post('verificarpago','PagoController@create3');

// servicios ordenestado
Route::get('listado_ordenes_pendiente/{id_firebase}/{idioma}','OrdenestadoController@show');
Route::get('listado_ordenes/{id_firebase}/{idioma}','OrdenestadoController@show2');
Route::get('verorden/{id_firebase}/{ordenid}','OrdenestadoController@show3');
Route::get('ordenestadovideollamada/{id_firebase1}/{id_firebase2}/{idioma}','OrdenestadoController@show4');
Route::get('listado_pedidos/{id_firebase}/{idioma}','OrdenestadoController@show5');
Route::get('verpedido/{id_firebase}/{ordenid}','OrdenestadoController@show6');

// servicios orden
Route::post('orden','OrdenController@create');
Route::get('ListadodeOrdenesPendientesPaginado','OrdenestadoController@show_1');
Route::get('ListadodeOrdenesPaginado','OrdenestadoController@show2_1');
Route::get('BuscarOrden','OrdenestadoController@show2_2');
Route::get('ListadodePedidosPaginado','OrdenestadoController@show5_1');
Route::get('BuscarPedido','OrdenestadoController@show5_2');
// servicios deposito orden
Route::get('depositoorden/{id_firebase}','DepositoordenController@show');

// servicios orden cambia estado
Route::post('ordenestadoentregado/{idioma}','OrdencambiaestadoController@create');
Route::post('ordenestadorecibido/{idioma}','OrdencambiaestadoController@create2');

// servicios videos
Route::get('videos/{id_firebase}/{idioma}','VideosController@show');
Route::get('oldallvideos/{idservicio}/{idioma}','VideosController@show2');
Route::get('allvideos2/{idservicio}/{idioma}','VideosController@show2_1');
Route::post('videos/{id_firebase}','VideosController@create');
Route::put('videos/{id}','VideosController@update');
Route::delete('videos/{id}','VideosController@destroy');
// test
Route::get('haversine','VideosController@haversine');
Route::get('getBoundaries','VideosController@getBoundaries');

// new get allvideos
Route::get('profile','VideosController@showordenespagadas');
Route::get('videosbyuser','VideosController@shownew');
Route::get('videosbusqueda','VideosController@shownewbusqueda');
Route::get('allvideos','VideosController@showallvideos');
Route::get('allvideosbusqueda','VideosController@showallvideosbusqueda');

// vimeo
Route::get('vimeo','VideosController@show3');
Route::get('allvimeo','VideosController@show4');

// servicios de Traducciones
Route::get('traduccion/{idioma}','TraduccionController@show');

// servicios Horaserver
Route::get('horaserver','HoraserverController@show');

// servicios de Transaccions
Route::get('transaccion','TransaccionController@show');

// servicios de optionvaluemix
Route::get('optionvaluemixall','OptionvaluemixController@show');
Route::get('optionvaluemix','OptionvaluemixController@show2');

// servicios de paymentmodel
Route::get('paymentmodeltype','PaymentmodelController@show');

// servicios para la app web
Route::post('userweb','Auth\RegisterController@create');
Route::post('login','Auth\LoginController@login');
Route::post('leerUsuario','Auth\LoginController@leerUsuario');
// en proceso toma tiempo
Route::post('userweb2','Auth\RegisterController@register');

Route::get('/', function () {
    return view('welcome');
});
