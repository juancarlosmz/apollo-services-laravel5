<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCuentabancariasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentabancarias', function (Blueprint $table) {
            $table->increments('id');
            $table->string("numero");
            $table->string('banco');
            $table->integer('userId')->unsigned();
            $table->timestamps();
            $table->foreign('userId')
				  ->references('id')
                  ->on('usuarios'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuentabancarias');
    }
}
