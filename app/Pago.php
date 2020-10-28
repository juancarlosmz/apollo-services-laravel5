<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Pago extends Model
{
    use Notifiable;
    protected $table = 'pagos';
    protected $fillable = [
        'value','fecha','ordenId','cliente_isocurrency','cliente_tc','business_isocurrency','business_tc'
    ];
}
