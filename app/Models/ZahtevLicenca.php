<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $osoba
 * @property string $licencatip
 * @property string $licenca_broj
 * @property int $strucniispit
 * @property int $vrsta_posla_id
 * @property string $reg_oblast_id
 * @property int $reg_pod_oblast_id
 * @property int $referenca1
 * @property int $referenca2
 * @property int $pecat
 * @property string $datum
 * @property string $status
 * @property string $razlog
 * @property string $prijem
 * @property string $preporuka2
 * @property string $barcode
 * @property string $preporuka1
 * @property int $mestopreuzimanja
 * @property string $status_pregleda
 * @property string $datum_statusa_pregleda
 * @property string $licenca_datum_resenja
 * @property string $licenca_broj_resenja
 * @property int $request_category_id
 * @property int $zvanje_id
 * @property Licencatip $tipLicence
 * @property Osoba $osobaId
 * @property Licenca $licenca
 * @property VrstaPosla $vrstaPosla
 * @property RegOblast $regOblast
 * @property RegPodoblast $regPodoblast
 * @property PrijavaClanstvo $prijavaClan
 * @property RequestCategory $requestCategory
 * @property Zvanje $zvanje
 * @property Document[] $documents
 */
class ZahtevLicenca extends Model
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
    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = TRUE;

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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function licencaOsoba()
    {
        return $this->hasOne('App\Models\Licenca', 'osoba', 'osoba');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prijavaClan()
    {
        return $this->belongsTo('App\Models\ClanPrijava', 'prijava_clan_id');
    }

    public function siPrijava()
    {
        return $this->belongsTo('App\Models\SiPrijava', 'si_prijava_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\morphOne
     */
    public function log()
    {
        return $this->morphOne('App\Models\Log', 'loggable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function statusId()
    {
        return $this->belongsTo('App\Models\Status', 'status');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requestCategory()
    {
        return $this->belongsTo('App\Models\RequestCategory');
    }

    public function zvanje(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\Zvanje');
    }

    /**
     * Get all of the request's documents.
     */
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Get all of the licence's requests.
     */
    public function requests()
    {
        return $this->morphMany(Request::class, 'requestable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reference()
    {
        return $this->belongsToMany('App\Models\Referenca', 'referenca_licenca_zahtev', 'licenca_zahtev_id', 'referenca_id')
            ->using('App\Models\ReferencaLicencaZahtev')
            ->withPivot([
                'created_at',
                'updated_at',
            ]);
    }
}
