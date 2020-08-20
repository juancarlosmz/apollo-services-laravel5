<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transaccion extends Model
{
    //
    use Notifiable;
    protected $table = 'transaccions';
    protected $fillable = [
        'userIdvendedor','userIdcomprador','ordenId'
    ];
    public function usuario() {
      return $this->belongsTo(Usuario::class, 'userIdvendedor');
    } 
    public function usuario2() {
        return $this->belongsTo(Usuario::class, 'userIdcomprador');
      } 
    public function orden() {
        return $this->belongsTo(Orden::class, 'ordenId');
    }
}
