<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_firebase');
            $table->timestamp("fecha_nacimiento");
            $table->string('sexo');
            $table->integer('tipouserId')->unsigned();
            $table->integer('servicioId')->unsigned();
            $table->timestamps();
            $table->foreign('tipouserId')
				  ->references('id')
                  ->on('tipousers');
            $table->foreign('servicioId')
				  ->references('id')
				  ->on('servicios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}
