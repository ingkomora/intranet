<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string osiguranje_vrsta
 * @property int osiguranje_tip_id
 * @property string osiguravajuca_kuca_mb
 * @property string ugovarac_osiguranja_mb
 * @property string polisa_broj
 * @property string polisa_predmet
 * @property int polisa_pokrice_id
 * @property string polisa_iskljucenost
 * @property string polisa_teritorijalni_limit
 * @property string polisa_datum_izdavanja
 * @property string polisa_datum_pocetka
 * @property string polisa_datum_zavrsetka
 * @property int status_polise_id
 * @property int status_dokumenta_id
 * @property string napomena
 * @property string created_at
 * @property string updated_at
 * @property string ugovarac_osiguranja_osoba_id
 */
class Osiguranje extends Model
{

    use CrudTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'osiguranja';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['osiguranje_tip_id', 'osiguravajuca_kuca_mb', 'ugovarac_osiguranja_mb', 'ugovarac_osiguranja_osoba_id', 'osiguranje_vrsta', 'polisa_broj', 'polisa_predmet', 'polisa_pokrice_id', 'polisa_iskljucenost', 'polisa_teritorijalni_limit', 'polisa_datum_izdavanja', 'polisa_datum_pocetka', 'polisa_datum_zavrsetka', 'status_polise_id', 'status_dokumenta_id', 'napomena', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = TRUE;

    public $identifiableAttribute = 'polisa_broj';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function osiguranjeTip()
    {
        return $this->belongsTo('App\Models\OsiguranjeTip', 'osiguranje_tip_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firmaOsiguravajucaKuca()
    {
        return $this->belongsTo('App\Models\Firma', 'osiguravajuca_kuca_mb', 'mb');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firmaUgovarac()
    {
        return $this->belongsTo('App\Models\Firma', 'ugovarac_osiguranja_mb', 'mb');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function osobaUgovarac()
    {
        return $this->belongsTo('App\Models\Osoba', 'ugovarac_osiguranja_osoba_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function polisaPokrice()
    {
        return $this->belongsTo('App\Models\OsiguranjePolisaPokrice', 'polisa_pokrice_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function statusPolise()
    {
        return $this->belongsTo('App\Models\Status', 'status_polise_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function statusDokumenta()
    {
        return $this->belongsTo('App\Models\Status', 'status_dokumenta_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function osobe()
    {
        return $this->belongsToMany('App\Models\Osoba', 'osiguranje_osoba', 'osiguranja_id', 'osoba_id')
            ->using('App\Models\OsiguranjeOsoba')
            ->withPivot([
                'datum_provere',
                'created_at',
                'updated_at',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /*
     * Metoda proverava datume polise i status dokumenta
     *
     * @return boolean
     */

    public function validnaPolisa()
    {
        /*$this->polisa_datum_pocetka = Carbon::parse($this->polisa_datum_pocetka)->addDay()->format('Y-m-d');
        $this->polisa_datum_zavrsetka = Carbon::parse($this->polisa_datum_zavrsetka)->addDay()->format('Y-m-d');*/
        if ($this->polisa_datum_pocetka <= now() and $this->polisa_datum_zavrsetka >= now() and $this->status_dokumenta_id == DOKUMENT_ORIGINAL) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getOsiguranjeDataAttribute()
    {
        $this->status_polise_id = $this->status_polise_id === 1 ? 'A' : 'N';
        $this->polisa_datum_pocetka = !empty($this->polisa_datum_pocetka) ? Carbon::parse($this->polisa_datum_pocetka)->format('d.m.Y') : '-';
        $this->polisa_datum_zavrsetka = !empty($this->polisa_datum_zavrsetka) ? Carbon::parse($this->polisa_datum_zavrsetka)->format('d.m.Y') : '-';

        if (!empty($this->firmaUgovarac)) {
            return "{$this->firmaUgovarac->naziv} [ $this->polisa_broj ($this->status_polise_id) od $this->polisa_datum_pocetka do $this->polisa_datum_zavrsetka ]";
        }
        if (!empty($this->ugovarac_osiguranja_osoba_id)) {
            return "{$this->osobaUgovarac->full_name} [ $this->polisa_broj ($this->status_polise_id) od $this->polisa_datum_pocetka do $this->polisa_datum_zavrsetka ]";
        }
    }
}
