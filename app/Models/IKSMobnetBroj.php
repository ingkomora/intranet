<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $broj
 * @property string $korisnik
 * @property string $datum_ugovora
 * @property string $pin
 * @property string $puk
 * @property string $imsi
 * @property string $tip_kartice
 * @property string $broj_ugovora
 * @property int $iznos_limit
 * @property integer $status
 * @property integer $profil
 * @property integer $roaming
 * @property integer $gprs
 * @property integer $vas
 * @property integer $mparking
 * @property integer $nagradna_igra
 * @property integer $data_rom
 * @property integer $biz_plus
 * @property integer $lte
 * @property string $dodaci
 * @property string $korisnik_servisa
 * @property integer $korisnik_servisa_je_platilac
 * @property string $zbirni_racun_platioca
 * @property string $iccid
 * @property IKSMobnetZahtev[] $zahtevi
 */
class IKSMobnetBroj extends Model
{
    use CrudTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tiksmobnetbrojevi';

    public $timestamps = FALSE;

    /**
     * @var array
     */
    protected $guarded = ['id'];


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zahtevi()
    {
        return $this->hasMany('App\Models\IKSMobnetZahtev', 'broj_id', 'broj');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
