<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Traduccion extends Model{
    use Notifiable;
    protected $table = 'traduccions';
    protected $fillable = [
        'descripcion','idiomaId','validiomaId'
    ];
    public function idiomas() {
        return $this->belongsTo(Idiomas::class, 'idiomaId');
    }
    public function valuesidiomas() {
        return $this->belongsTo(Valuesidiomas::class, 'validiomaId');
    }
}
