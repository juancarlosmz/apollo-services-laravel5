<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateServiciousuariosTable extends Migration{
    public function up(){
        Schema::create('serviciousuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId')->unsigned();
            $table->integer('serviciosId')->unsigned();
            $table->timestamps();
            $table->foreign('userId')
				  ->references('id')
                  ->on('usuarios'); 
            $table->foreign('serviciosId')
				  ->references('id')
                  ->on('servicios');      
        });
    }
    public function down(){
        Schema::dropIfExists('serviciousuarios');
    }
}
