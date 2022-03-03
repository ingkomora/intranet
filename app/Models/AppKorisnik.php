<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $regkanc_id
 * @property int $regkanc_id2
 * @property string $nalog
 * @property string $lozinka
 * @property string $uloge
 * @property string $ime
 * @property string $prezime
 * @property string $sesija
 * @property string $status
 * @property SiPrijava[] $siPrijave
 * @property SiKomisijasaziv[] $siKomisijaSazivi
 */
class AppKorisnik extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tappkorisnik';

    /**
     * @var array
     */
    protected $fillable = ['regkanc_id', 'regkanc_id2', 'nalog', 'lozinka', 'uloge', 'ime', 'prezime', 'sesija', 'status'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siPrijave()
    {
        return $this->hasMany('App\Models\SiPrijava', 'app_korisnik_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function siKomisijaSazivi()
    {
        return $this->belongsToMany('App\Models\SiKomisijaSaziv', 'tsi_komisija_appkorisnik', 'user_id', 'komisijasaziv_id');
    }
}
