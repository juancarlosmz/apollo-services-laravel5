<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateOrdensTable extends Migration{
    public function up(){
        Schema::create('ordens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('numero')->increments('numero',5000);
            $table->string('descripcion');
            $table->decimal('total', 9, 2);
            $table->timestamp("fechaentrega");
            $table->timestamps();
        });
    }
    public function down(){
        Schema::dropIfExists('ordens');
    }
}
