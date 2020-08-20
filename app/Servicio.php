<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Servicio extends Model{
     use Notifiable;
     protected $table = 'servicios';
     protected $fillable = [
         'validiomaId','idiomaId'
     ];
     public function idioma() {
        return $this->belongsTo(Idiomas::class, 'idiomaId');
      }
}
