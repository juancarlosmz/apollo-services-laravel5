<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Isos extends Model{
    use Notifiable;
    protected $fillable = [
        'en'
    ];
}
