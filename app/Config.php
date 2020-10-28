<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Config extends Model{
    use Notifiable;
    //Stripe
    protected static function StripeConfig() {
        $data = [
            'StripeApiKey' => 'sk_test_3RP8Mjx7h8bC6IeVcqOaSDFA'
        ];
        return $data;    
        //Config::StripeConfig()['StripeApiKey']
    }
    //Vimeo
    protected static function VimeoConfig() {
        $data = [
            'client_id' => '775c22cdd9fc4659ce5e3d8b60983ea494d5f651',
            'client_secret' => 'EvxF5vcxCLcmYDoTdY14Je1WuDG7fVolfehkXEOi9ZtkWr1NXcrCwmdlrOSYj6uwKwkFvZGHRXmHW8NzD8ub5wNItDpoQBbs062//5f+8FNWUnXU2sxrOJrdjMUpqJ9a',
            'access_token' => '9a57070414710af364956455ef45bd7f'
        ];
        return $data;    
        //Config::VimeoConfig()['client_id']
        //Config::VimeoConfig()['client_secret']
        //Config::VimeoConfig()['access_token']
    }
}
