<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Usuario extends Model{
    use Notifiable;
    protected $table = 'usuarios';
    protected $fillable = [
        'id_firebase','fecha_nacimiento','name','photo','sexo','servicioId','idiomaId','phone'
    ];
    public function servicio() {
      return $this->belongsTo(Servicio::class, 'servicioId');
    }
    public function idioma() {
      return $this->belongsTo(Idiomas::class, 'idiomaId');
    }
}
