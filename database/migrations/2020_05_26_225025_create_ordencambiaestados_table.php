<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdencambiaestadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordencambiaestados', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ordenId')->unsigned();
            $table->integer('ordenestadoId')->unsigned();
            $table->timestamps();
            $table->foreign('ordenId')
				  ->references('id')
                  ->on('ordens');
            $table->foreign('ordenestadoId')
				  ->references('id')
                  ->on('ordenestados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordencambiaestados');
    }
}
