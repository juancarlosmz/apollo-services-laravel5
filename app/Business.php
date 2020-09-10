<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Business extends Model{
    use Notifiable;
    protected $table = 'businesses';
    protected $fillable = [
        'userId','descripcion','logo','details','lat','lng','direccion','pais','ciudad','zip'
    ];
    public function usuario() {
      return $this->belongsTo(Usuario::class, 'userId');
    } 
}
