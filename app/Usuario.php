<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Usuario extends Model
{
    //
    use Notifiable;
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_firebase','fecha_nacimiento','sexo','tipouserId','servicioId'
    ];
    public function servicio() {
      return $this->belongsTo(Servicio::class, 'servicioId');
    }
    public function tipouser() {
      return $this->belongsTo(Tipouser::class, 'tipouserId');
    }
    public function deposito() {
      return $this->hasMany(Deposito::class, 'Id');
    }
}
