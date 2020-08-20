<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Videos extends Model
{
    //
    use Notifiable;
    protected $table = 'videos';
    protected $fillable = [
        'userId','titlevideo','precio','urlvideo','urlimagen'
    ];
    public function usuario() {
      return $this->belongsTo(Usuario::class, 'userId');
    } 
}
