<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property Licenca $licenca
 * @property Opstina $opstina
 * @property Firma $firma
 */
class PromenaPodataka extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tprijavapromenapodataka';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    // mapiranje polja iz zahteva za promenu ka poljima u tabeli tosoba
    public
        $osoba_related_fields = [
        'adresa' => 'prebivalisteadresa',
        'mesto' => 'prebivalistemesto',
        'pbroj' => 'prebivalistebroj',
        'topstina_id' => 'prebivalisteopstinaid',
        'tel' => 'kontakttel',
        'mob' => 'mobilnitel',
        'email' => 'kontaktemail',
        'nazivfirm' => 'firmanaziv',
        'mestofirm' => 'firmamesto',
        'opstinafirm' => 'firmaopstina',
        'emailfirm' => 'firmaemail',
        'telfirm' => 'firmatel',
        'wwwfirm' => 'firmaweb',
        'mbfirm' => 'firma_mb',
    ],
        $public_fields = [
        'adresa' => 'Adresa',
        'mesto' => 'Mesto',
        'pbroj' => 'Poštanski broj',
        'topstina_id' => 'Opština',
        'tel' => 'Telefon',
        'mob' => 'Mobilni telefon',
        'email' => 'Email',
        'nazivfirm' => 'Naziv firme',
        'mestofirm' => 'Mesto firme',
        'opstinafirm' => 'Opština firme',
        'adresafirm' => 'Opština firme',
        'emailfirm' => 'Email firme',
        'telfirm' => 'Telefon firme',
        'wwwfirm' => 'Web firme',
        'mbfirm' => 'MB firme',
        'pibfirm' => 'PIB firme',
    ],
        // mapiranje polja iz zahteva za promenu ka poljima u tabeli firme
        $firma_related_fields = [
        'mbfirm' => 'mb',
        'pibfirm' => 'pib',
        'nazivfirm' => 'naziv',
        'mestofirm' => 'mesto',
        'adresafirm' => 'adresa',
        'opstinafirm' => 'opstina_id',
        'telfirm' => 'telefon',
        'emailfirm' => 'email',
        'wwwfirm' => 'web',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function licenca()
    {
        return $this->belongsTo('App\Models\Licenca', 'brlic', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function opstina()
    {
        return $this->belongsTo('App\Models\Opstina', 'topstina_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firma()
    {
        return $this->belongsTo('App\Models\Firma', 'mb');

    }
}
