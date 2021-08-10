<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $titula
 * @property string $ime
 * @property string $prezime
 * @property string $zvanje
 * @property string $kontakttel
 * @property string $mobilnitel
 * @property string $kontaktemail
 * @property string $firma
 * @property string $napomena
 * @property SiKomisijaKomisijaclan[] $siKomisijaClanoviKomisije
 * @property SiKandidatKomisijaclan[] $siKandidatClanoviKomisije
 */
class SiKomisijaclan extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'si_komisijaclan';

    /**
     * @var array
     */
    protected $fillable = ['titula', 'ime', 'prezime', 'zvanje', 'kontakttel', 'mobilnitel', 'kontaktemail', 'firma', 'napomena'];

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
        return $this->hasMany('App\Models\SiKomisijaKomisijaclan', 'komisijaclan_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siKandidatClanoviKomisije()
    {
        return $this->hasMany('App\Models\SiKandidatKomisijaclan', 'komisijaclan_id');
    }
}
