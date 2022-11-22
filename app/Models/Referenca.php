<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $osoba
 * @property int $vrstaplana
 * @property int $uloga
 * @property int $broj
 * @property float $godinaizrade
 * @property float $godinausvajanja
 * @property string $lokacijamesto
 * @property string $lokacijaopstina
 * @property string $lokacijadrzava
 * @property string $firma
 * @property string $naziv
 * @property string $lokacijaadresa
 * @property string $investitor
 * @property string $odgprojektant
 * @property VrstaPlana $vrstaPlana
 * @property Uloga $ulogaId
 * @property Osoba $tosobaId
 * @property Licenca $licencaOdgovornoLice
 * @property SiPrijava[] $siPrijave
 * @property ZahtevLicenca[] $zahteviLicence
 */
class Referenca extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'treferenca';

    /**
     * @var array
     */
    protected $fillable = ['osoba', 'vrstaplana', 'uloga', 'broj', 'godinaizrade', 'godinausvajanja', 'lokacijamesto', 'lokacijaopstina', 'lokacijadrzava', 'firma', 'naziv', 'lokacijaadresa', 'investitor', 'odgprojektant', 'odgovorno_lice_licenca_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = TRUE;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vrstaPlana()
    {
        return $this->belongsTo('App\Models\VrstaPlana', 'vrstaplana');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ulogaId()
    {
        return $this->belongsTo('App\Models\Uloga', 'uloga');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function osobaId()
    {
        return $this->belongsTo('App\Models\Osoba', 'osoba');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function licencaOdgovornoLice()
    {
        return $this->belongsTo('App\Models\Licenca', 'odgovorno_lice_licenca_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function siPrijave()
    {
        return $this->belongsToMany('App\Models\SiPrijava', 'referenca_si_prijava', 'referenca_id', 'si_prijava_id')
            ->using('App\Models\ReferencaSiPrijava')
            ->withPivot([
                'created_at',
                'updated_at',
            ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function zahteviLicence()
    {
        return $this->belongsToMany('App\Models\ZahtevLicenca', 'referenca_licenca_zahtev', 'referenca_id', 'licenca_zahtev_id')
            ->using('App\Models\ReferencaLicencaZahtev')
            ->withPivot([
                'created_at',
                'updated_at',
            ]);
    }

    public function getDataReferenceToArrayAttribute(): string
    {
//        $odgovorno_lice = Licenca::find($this->odgovorno_lice_licenca_id);
        $odgovorno_lice = Licenca::where('id',$this->odgovorno_lice_licenca_id)->first();
        // todo: dodati jos informacija ako treba
        return !is_null($odgovorno_lice) ? $odgovorno_lice->osobaId->ime_prezime_licence : '-';
    }

}
