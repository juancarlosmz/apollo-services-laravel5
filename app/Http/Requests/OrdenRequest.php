<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class OrdenRequest extends FormRequest{
    public function authorize(){
        return true;
    }
    public function rules(){
        return [
            'descripcion' => 'required',
            'total' => 'required',
            'fechaentrega' => 'required',
        ];
    }
}
