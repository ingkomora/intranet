<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Tesla\JMBG\JMBG;

class OsobaRequest extends FormRequest {
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
//        $value = \request('id');
        return [
            'id' => [
                'required',
                'size:13',
//                Rule::unique('tosoba')->ignore('id'),
//                Rule::unique('tosoba')->ignore($value),//ne radi
                function ($attribute, $value, $fail) {
                    if (!JMBG::for($value)->isValid()) {
                        $fail('JMBG: <strong>' . $value . '</strong> nije ispravan, proverite unos.');
                    }
                }
            ],
            'zvanjeId' => 'required',
            'ime' => 'required',
            'prezime' => 'required',
            'roditelj' => 'required',
            'rodjenjemesto' => 'required',
            'rodjenjeopstina' => 'required',
            'rodjenjedrzava' => 'required',
            'prebivalistebroj' => 'required',
            'prebivalistemesto' => 'required',
            'opstinaId' => 'required',
            'prebivalisteadresa' => 'required',
            'firma' => 'required',
            'diplfakultet' => 'required',
            'diplmesto' => 'required',
            'dipldrzava' => 'required',
            'diplgodina' => 'required'
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
