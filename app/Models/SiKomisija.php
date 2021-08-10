<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $struka_id
 * @property string $naziv
 * @property string $opis
 * @property Tstrucniispitstruka $tstrucniispitstruka
 * @property SiKomisijaoblast[] $siKomisijaOblasti
 * @property SiKomisijasaziv[] $siKomisijaSazivi
 */
class SiKomisija extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'si_komisija';

    /**
     * @var array
     */
    protected $fillable = ['struka_id', 'naziv', 'opis'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tstrucniispitstruka()
    {
        return $this->belongsTo('App\Models\Tstrucniispitstruka', 'struka_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siKomisijaOblasti()
    {
        return $this->hasMany('App\Models\SiKomisijaoblast', 'komisija_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siKomisijaSazivi()
    {
        return $this->hasMany('App\Models\SiKomisijasaziv', 'komisija_id');
    }
}
