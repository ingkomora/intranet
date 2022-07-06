<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RequestRequest extends FormRequest
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
//            TODO ovo ne radi
             'status' => Rule::notIn([41,43,99,100,200]),
//             'status' => Rule::requiredIf($request->user()->is_admin)Rule::notIn([41,43,99,100,200]),
//             'status_id' => 'required,not_in:41,43,99,100,200', // TODO: privremeno da bi marko mogao da oznaci zahteve (resenja) na koje je ulozena zalba
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
