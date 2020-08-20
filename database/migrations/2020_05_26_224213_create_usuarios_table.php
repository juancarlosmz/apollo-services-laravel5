<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateUsuariosTable extends Migration{
    public function up(){
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_firebase');
            $table->timestamp("fecha_nacimiento")->nullable();
            $table->string('sexo')->nullable();
            //$table->integer('servicioId')->unsigned();
            $table->integer('idiomaId')->unsigned();
            $table->timestamps();
            /*$table->foreign('servicioId')
				  ->references('id')
                  ->on('servicios');*/
            $table->foreign('idiomaId')
				  ->references('id')
				  ->on('idiomas');      
        });
    }
    public function down(){
        Schema::dropIfExists('usuarios');
    }
}
