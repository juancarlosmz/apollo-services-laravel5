<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenestadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenestados', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion');
            $table->integer('validiomaId')->unsigned();
            $table->timestamps();
            $table->foreign('validiomaId')
				  ->references('id')
                  ->on('valuesidiomas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenestados');
    }
}
