<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransaccionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaccions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userIdvendedor')->unsigned();
            $table->integer('userIdcomprador')->unsigned();
            $table->integer('ordenId')->unsigned();
            $table->timestamps();
            $table->foreign('userIdvendedor')
				  ->references('id')
                  ->on('usuarios');
            $table->foreign('userIdcomprador')
				  ->references('id')
                  ->on('usuarios');      
            $table->foreign('ordenId')
				  ->references('id')
                  ->on('ordens');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaccions');
    }
}
