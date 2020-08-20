<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Orden extends Model{
    use Notifiable;
    protected $table = 'ordens';
    protected $fillable = [
        'numero','descripcion','total','fechaentrega'
    ];
}
