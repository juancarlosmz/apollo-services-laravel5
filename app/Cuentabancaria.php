<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Cuentabancaria extends Model
{
    use Notifiable;
    protected $table = 'cuentabancarias';
    protected $fillable = [
        'numero','banco','userId'
    ];
    public function usuario() {
      return $this->belongsTo(Usuario::class, 'userId');
    } 
}
