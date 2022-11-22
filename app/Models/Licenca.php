<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $osoba
 * @property int $zahtev
 * @property string $datum
 * @property string $datumuo
 * @property string $datumobjave
 * @property string $status
 * @property string $datumukidanja
 * @property string $razlogukidanja
 * @property int $preuzeta
 * @property int $mirovanje
 * @property int $prva
 * @property int $prijava_clan_id
 * @property string $broj_resenja
 * @property string $licencatip
 * @property Osoba $osobaId
 * @property LicencaTip $tipLicence
 * @property ZahtevLicenca $zahtevId
 */
class Licenca extends Model
{
    use CrudTrait;

    protected $table = 'tlicenca';
    protected $primaryKey = 'idn';
//    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
//    public $incrementing = FALSE;

    /**
     * @var array
     */
    protected $guarded = ['idn'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = TRUE;

    public $identifiableAttribute = 'idn';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *  TODO rename FK osoba
     */
    public function osobaId()
    {
        return $this->belongsTo('App\Models\Osoba', 'osoba');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     */
    public function tipLicence()
    {
        return $this->belongsTo('App\Models\LicencaTip', 'licencatip');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zahtevId()
    {
        return $this->belongsTo('App\Models\ZahtevLicenca', 'zahtev');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clanarineOld()
    {
        return $this->hasMany('App\Models\ClanarinaOld', 'brlicence');
    }

    public function requestable()
    {
        return $this->morphMany(Request::class, 'requestable');

    }



    public function getImePrezimeJmbgAttribute()
    {
        return $this->osobaId->ime . " " . $this->osobaId->prezime . " (" . $this->osobaId->id . ")";
    }

    public function getImeRoditeljPrezimeAttribute()
    {
        return "{$this->osobaId->ime} ({$this->osobaId->roditelj}) {$this->osobaId->prezime}";
    }
}
