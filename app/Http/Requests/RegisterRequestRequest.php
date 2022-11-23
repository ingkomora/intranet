<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'osoba' => 'required',
            'requestCategory' => 'required',
            'status' => 'required',
            'licenca' => 'required_if:requestCategory,3'
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'osoba' => 'Ime i prezime',
            'requestCategory' => 'Kategorija zahteva',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'licenca.required_if' => 'Polje licenca je potrebno kada polje Kategorija zahteva ima vrednost "Svečana forma licence".'
        ];
    }
}
