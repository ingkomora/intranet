<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $osoba
 * @property int $mandat_id
 * @property int $funkcija_id
 * @property int $region_id
 * @property int $sekcija_id
 * @property int $status
 * @property string $foto
 * @property string $cv
 * @property string $napomena
 * @property Osoba $osobaId
 * @property Funkcija $funkcija
 * @property FunkcionerMandat $funkcionerMandat
 * @property Region $region
 * @property Sekcija $sekcija
 */
class Funkcioner extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tfunkcioner';

    /**
     * @var array
     */
    protected $fillable = ['funkcija_id', 'region_id', 'sekcija_id', 'status', 'foto', 'cv', 'napomena'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * TODO rename FK osoba
     */
    public function osobaId()
    {
        return $this->belongsTo('App\Models\Osoba', 'osoba');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function funkcija()
    {
        return $this->belongsTo('App\Models\Funkcija', 'funkcija_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function funkcionerMandat()
    {
        return $this->belongsTo('App\Models\FunkcionerMandat', 'mandat_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo('App\Models\Region', 'region_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sekcija()
    {
        return $this->belongsTo('App\Models\Sekcija', 'sekcija_id');
    }
}
