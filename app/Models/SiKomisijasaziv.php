<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $komisija_id
 * @property int $aktivna
 * @property string $datum_imenovanja
 * @property string $napomena
 * @property SiKomisija $siKomisija
 * @property SiKomisijaKomisijaclan[] $siKomisijaClanoviKomisije
 * @property Tappkorisnik[] $tappKorisnici
 * @property SiRok[] $siRokovi
 * @property SiKandidatKomisijaclan[] $siKandidatClanoviKomisije
 */
class SiKomisijasaziv extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'si_komisijasaziv';

    /**
     * @var array
     */
    protected $fillable = ['komisija_id', 'aktivna', 'datum_imenovanja', 'napomena'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siKomisija()
    {
        return $this->belongsTo('App\Models\SiKomisija', 'komisija_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siKomisijaClanoviKomisije()
    {
        return $this->hasMany('App\Models\SiKomisijaKomisijaclan', 'komisijasaziv_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tappKorisnici()
    {
        return $this->belongsToMany('App\Models\Tappkorisnik', 'si_komisija_appkorisnik', 'komisijasaziv_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siRokovi()
    {
        return $this->hasMany('App\Models\SiRok', 'komisijasaziv_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siKandidatClanoviKomisije()
    {
        return $this->hasMany('App\Models\SiKandidatKomisijaclan', 'komisijasaziv_id');
    }
}
