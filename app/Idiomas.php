<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Idiomas extends Model{
    use Notifiable;
    protected $table = 'idiomas';
    protected $fillable = [
        'descripcion'
    ];
}
