<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Valuesidiomas extends Model{
    use Notifiable;
    protected $table = 'valuesidiomas';
    protected $fillable = [
        'descripcion'
    ];
}
