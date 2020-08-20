<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositoordensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depositoordens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('depositoId')->unsigned();
            $table->integer('ordenId')->unsigned();
            $table->timestamps();
            $table->foreign('depositoId')
				  ->references('id')
                  ->on('depositos');
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
        Schema::dropIfExists('depositoordens');
    }
}
