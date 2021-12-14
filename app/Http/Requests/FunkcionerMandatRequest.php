<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class FunkcionerMandatRequest extends FormRequest
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
            'naziv' => 'required',
            'naziv_full' => 'required',
//            'naziv_cir' => 'required',
//            'naziv_full_cir' => 'required',
            'datum_od' => 'required',
//            'datum_do' => '',
//            'funkcioneri' => 'required', // relacija
            'funkcionerMandatTip' => 'required', // relacija
//            'napomena' => '',
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
            //
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
            //
        ];
    }
}
