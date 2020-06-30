<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $osiguranje_tip_id
 * @property integer $osiguravajuca_kuca_mb
 * @property integer $ugovarac_osiguranja_mb
 * @property string $osiguranje_vrsta
 * @property string $polisa_broj
 * @property string $polisa_predmet
 * @property int $polisa_pokrice_id
 * @property string $polisa_iskljucenost
 * @property string $polisa_teritorijalni_limit
 * @property string $polisa_datum_izdavanja
 * @property string $polisa_datum_pocetka
 * @property string $polisa_datum_zavrsetka
 * @property int $status_polise_id
 * @property int $status_dokumenta_id
 * @property string $napomena
 * @property string $created_at
 * @property string $updated_at
 * @property OsiguranjeTip $osiguranjeTip
 * @property Firma $firmaOsiguravajucaKuca
 * @property Firma $firmaUgovarac
 * @property Status $statusPolise
 * @property Status $statusDokumenta
 * @property Osoba[] $osobe
 */
class Osiguranje extends Model {

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
    protected $fillable = ['osiguranje_tip_id', 'osiguravajuca_kuca_mb', 'ugovarac_osiguranja_mb', 'osiguranje_vrsta', 'polisa_broj', 'polisa_predmet', 'polisa_pokrice_id', 'polisa_iskljucenost', 'polisa_teritorijalni_limit', 'polisa_datum_izdavanja', 'polisa_datum_pocetka', 'polisa_datum_zavrsetka', 'status_polise_id', 'status_dokumenta_id', 'napomena', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public $identifiableAttribute = 'polisa_broj';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function osiguranjeTip() {
        return $this->belongsTo('App\Models\OsiguranjeTip');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firmaOsiguravajucaKuca() {
        return $this->belongsTo('App\Models\Firma', 'osiguravajuca_kuca_mb', 'mb');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firmaUgovarac() {
        return $this->belongsTo('App\Models\Firma', 'ugovarac_osiguranja_mb', 'mb');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function polisaPokrice() {
        return $this->belongsTo('App\Models\OsiguranjePolisaPokrice', 'polisa_pokrice_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function statusPolise() {
        return $this->belongsTo('App\Models\Status', 'status_polise_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function statusDokumenta() {
        return $this->belongsTo('App\Models\Status', 'status_dokumenta_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function osobe() {
        return $this->belongsToMany('App\Models\Osoba', 'osiguranje_osoba', 'osiguranja_id', 'osoba_id')
            ->using('App\Models\OsiguranjeOsoba')
            ->withPivot([
                'datum_provere',
                'created_at',
                'updated_at',
            ]);
    }

    /*
     * Metoda proverava datume polise i status dokumenta
     *
     * @return boolean
     */

    public function validnaPolisa() {
        if ($this->polisa_datum_pocetka <= now() AND $this->polisa_datum_zavrsetka >= now() AND $this->status_dokumenta_id == DOKUMENT_ORIGINAL) {
            return true;
        } else {
            return false;
        }

    }
}
