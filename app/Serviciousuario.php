<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Serviciousuario extends Model{
    use Notifiable;
    protected $table = 'serviciousuarios';
    protected $fillable = [
        'userId','serviciosId'
    ];
    public function usuario() {
        return $this->belongsTo(Idiomas::class, 'userId');
    }
    public function servicio() {
        return $this->belongsTo(Servicio::class, 'serviciosId');
    }
}
