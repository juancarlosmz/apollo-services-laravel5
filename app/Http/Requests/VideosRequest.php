<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class VideosRequest extends FormRequest{
    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'userId' => 'required',
            'titlevideo' => 'required',
            'urlvideo' => 'required',
            'urlimagen' => 'required'
        ];
    }
}
