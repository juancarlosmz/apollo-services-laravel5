<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Paymentmodel extends Model{
    use Notifiable;
    protected $table = 'paymentmodel';
    protected $fillable = [
        'idorden','idpmtype'
    ];
}
