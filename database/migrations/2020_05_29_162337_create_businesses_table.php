<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateBusinessesTable extends Migration{
    public function up(){
        Schema::create('businesses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId')->unsigned();
            $table->string('descripcion')->nullable();
            $table->timestamps();
            $table->foreign('userId')
				  ->references('id')
                  ->on('usuarios');
        });
    }
    public function down(){
        Schema::dropIfExists('businesses');
    }
}
