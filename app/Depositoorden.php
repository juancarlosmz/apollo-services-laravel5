<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Depositoorden extends Model
{
    //
    use Notifiable;
    protected $table = 'depositoordens';
    protected $fillable = [
        'depositoId','ordenId'
    ];
    public function deposito() {
        return $this->belongsTo(Deposito::class, 'depositoId');
    } 
    public function orden() {
      return $this->belongsTo(Orden::class, 'ordenId');
    } 
}
