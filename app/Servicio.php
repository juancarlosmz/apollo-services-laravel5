<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Servicio extends Model
{
    //
     use Notifiable;
     protected $table = 'servicios';
 
     /**
      * The attributes that are mass assignable.
      *
      * @var array
      */
     protected $fillable = [
         'descripcion',
     ];

     public function usuario() {
		return $this->hasMany('App\Usuario');
     }
    
}
