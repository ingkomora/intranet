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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tlicenca';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = FALSE;

    /**
     * @var array
     */
    protected $fillable = ['id', 'osoba', 'zahtev', 'datum', 'datumuo', 'datumobjave', 'status', 'datumukidanja', 'razlogukidanja', 'preuzeta', 'mirovanje', 'prva', 'prijava_clan_id', 'broj_resenja', 'created_at', 'updated_at', 'licencatip', 'napomena', 'reg_oblast_id', 'reg_pod_oblast_id', 'vrsta_posla_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = TRUE;

    public $identifiableAttribute = 'id';

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



    public function getImePrezimeJmbgAttribute()
    {
        return $this->osobaId->ime . " " . $this->osobaId->prezime . " (" . $this->osobaId->id . ")";
    }
}
