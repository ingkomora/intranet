<?php

namespace App\Http\Requests;

use App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OsobaEditRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'ime' => 'required',
            'roditelj' => 'required',
            'prezime' => 'required',
            'prebivalisteadresa' => 'required',
            'prebivalistebroj' => 'required',
            'prebivalistemesto' => 'required',
            'prebivalistedrzava' => 'required',
            'prebivalisteopstinaid' => 'required',
            'ulica' => 'required',
            'broj' => 'required',
//            'status_id' => 'required',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes() {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages() {
        return [
            //
        ];
    }
}
