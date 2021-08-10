<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $naziv
 * @property SiKomisijaKomisijaclan[] $siKomisijaClanoviKomisije
 * @property SiKandidatKomisijaclan[] $siKandidatClanoviKomisije
 */
class SiKomisijafunkcija extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'si_komisijafunkcija';

    /**
     * @var array
     */
    protected $fillable = ['naziv'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siKomisijaClanoviKomisije()
    {
        return $this->hasMany('App\Models\SiKomisijaKomisijaclan', 'komisijafunkcija_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siKandidatClanoviKomisije()
    {
        return $this->hasMany('App\Models\SiKandidatKomisijaclan', 'komisijafunkcija_id');
    }
}
