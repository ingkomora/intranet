<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $osoba
 * @property float $licencatip
 * @property int $strucniispit
 * @property int $referenca1
 * @property int $referenca2
 * @property int $pecat
 * @property string $datum
 * @property string $status
 * @property string $razlog
 * @property string $prijem
 * @property string $preporuka2
 * @property string $preporuka1
 * @property int $mestopreuzimanja
 * @property string $status_pregleda
 * @property string $datum_statusa_pregleda
 * @property Licencatip $tipLicence
 * @property Osoba $osobaId
 * @property Licenca $licenca
 * @property ClanPrijava $prijavaClan

 */
class Zahtev extends Model
{
    use CrudTrait;
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tzahtev';

    /**
     * @var array
     */
    protected $fillable = ['osoba', 'licencatip', 'strucniispit', 'prijava_clan_id', 'referenca1', 'referenca2', 'pecat', 'datum', 'status', 'razlog', 'prijem', 'preporuka2', 'preporuka1', 'mestopreuzimanja', 'status_pregleda', 'datum_statusa_pregleda', 'licenca_broj', 'licenca_broj_resenja', 'licenca_datum_resenja', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = true;

    public $identifiableAttribute = 'id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *  * TODO rename FK
     */
    public function tipLicence()
    {
        return $this->belongsTo('App\Models\LicencaTip', 'licencatip');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *  * TODO rename FK
     */
    public function regLicencaTip()
    {
        return $this->belongsTo('App\Models\RegLicencaTip', 'licencatip');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *   TODO rename FK osoba
     */
    public function osobaId()
    {
        return $this->belongsTo('App\Models\Osoba', 'osoba');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function licenca()
    {
        return $this->hasOne('App\Models\Licenca', 'zahtev');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prijavaClan()
    {
        return $this->belongsTo('App\Models\ClanPrijava', 'prijava_clan_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\morphOne
     */
    public function log()
    {
        return $this->morphOne('App\Models\Log', 'loggable');
    }
}
