<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Ordencambiaestado extends Model
{
    //
    //
    use Notifiable;
    protected $table = 'ordencambiaestados';
    protected $fillable = [
        'ordenId','ordenestadoId'
    ];
    public function usuario() {
        return $this->belongsTo(Orden::class, 'ordenId');
      } 
    public function ordenestado() {
        return $this->belongsTo(Ordenestado::class, 'ordenestadoId');
    } 
    
}
