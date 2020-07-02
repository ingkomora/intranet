<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class OsiguranjeRequest extends FormRequest {
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
            'osiguranje_vrsta' => 'required',
            'polisaPokrice' => 'required',
            'firmaUgovarac' => 'required',
            'firmaOsiguravajucaKuca' => 'required',
            'osiguranjeTip' => 'required',
            'statusPolise' => 'required',
            'statusDokumenta' => 'required',
            'polisa_datum_pocetka' => 'required',
            'polisa_datum_zavrsetka' => 'required',
            'polisa_predmet' => 'required',
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
