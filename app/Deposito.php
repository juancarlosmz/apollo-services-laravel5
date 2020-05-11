<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Deposito extends Model
{
    use Notifiable;
    protected $table = 'depositos';
    protected $fillable = [
        'fecha_deposito','total','userId'
    ];
    public function usuario() {
      return $this->belongsTo(Usuario::class, 'userId');
    } 
}
