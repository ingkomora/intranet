<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property float $id
 * @property int $sekcija
 * @property string $naziv
 * @property string $status
 * @property string $idn
 * @property string $oznaka
 * @property string $opis
 * @property string $generacija
 * @property string $pod_oblast_id
 * @property VrstaPosla $vrstaPosla
 * @property LicencaVrsta $vrstaLicence
 * @property ZahtevLicenca[] $zahteviLicence
 * @property Licenca[] $licence
 * @property RegPodoblast $podOblast
 */
class LicencaTip extends Model
{
    use CrudTrait;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tlicencatip';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'float';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['sekcija', 'naziv', 'status', 'idn', 'oznaka', 'opis', 'generacija', 'pod_oblast_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return BelongsTo
     */
    public function vrstaLicence(): BelongsTo {
        return $this->belongsTo('App\Models\LicencaVrsta', 'sekcija');
    }

    /**
     * @return BelongsTo
     */
    public function vrstaPosla(): BelongsTo {
        return $this->belongsTo('App\Models\VrstaPosla', 'sekcija');
    }

    /**
     * @return HasMany
     */
    public function zahtevi(): HasMany {
        return $this->hasMany('App\Models\ZahtevLicenca', 'licencatip');
    }

    /**
     * @return HasMany
     */
    public function licence(): HasMany {
        return $this->hasMany('App\Models\Licenca', 'licencatip');
    }

    /**
     * @return BelongsTo
     */
    public function podOblast(): BelongsTo {
        return $this->belongsTo('App\Models\RegPodoblast', 'pod_oblast_id');
    }

    /**
     * Get the user's Full name.
     *
     * @param string $value
     * @return string
     */
    public function getOznakaNazivAttribute() {
        return "{$this->oznaka} - {$this->naziv}";
    }

    /**
     * Get the user's Full name.
     *
     * @param string $value
     * @return string
     */
    public function getTipNazivAttribute() {
        return "{$this->id} - {$this->naziv}";
    }

    /**
     * Get the user's Full name.
     *
     * @param string $value
     * @return string
     */
    public function getGenTipNazivAttribute() {
        return "{$this->id} - {$this->naziv} [Generacija: {$this->generacija}]";
    }

    public function getGenTipAttribute() {
        return "{$this->id} [Generacija: {$this->generacija}]";
    }

    public function getGenTipOznakaAttribute() {
        return "{$this->id} - {$this->oznaka} [Generacija: {$this->generacija}]";
    }

    public function getOznakaTipAttribute() {
        return "{$this->oznaka} ({$this->id})";
    }

    public function getTipNazivOznakaGenAttribute() {
        return "{$this->id} - {$this->naziv} ({$this->oznaka}) [Generacija: {$this->generacija}]";
    }
}
