<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraduccionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traduccions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion');
            $table->integer('idiomaId')->unsigned();
            $table->integer('validiomaId')->unsigned();
            $table->timestamps();
            $table->foreign('idiomaId')
				  ->references('id')
                  ->on('idiomas');
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
        Schema::dropIfExists('traduccions');
    }
}
