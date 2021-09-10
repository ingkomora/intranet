<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class OsiguranjeRequest extends FormRequest
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
        // fallback to global request instance
        /*        if (is_null($request)) {
                    $request = \Request::instance();
                }*/
        return [
            'osiguranje_vrsta' => 'required',
            'polisa_broj' => 'required',
            'polisaPokrice' => 'required', // polisa_pokrice_id
            'FirmaOsiguravajucaKuca' => 'required',
            'osobe' => 'required', // korisnici_osiguranja
            'ugovarac_osiguranja_mb' => 'required_without:ugovarac_osiguranja_osoba_id|required_unless:osiguranje_tip_id,' . OSIGURANJE_INDIVIDUALNO,
            'ugovarac_osiguranja_osoba_id' => 'required_without:ugovarac_osiguranja_mb|required_if:osiguranje_tip_id,' . OSIGURANJE_INDIVIDUALNO,
            'osiguranje_tip_id' => 'required',
            'status_polise_id' => 'required',
            'status_dokumenta_id' => 'required',
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
