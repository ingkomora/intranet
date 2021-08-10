<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $komisijasaziv_id
 * @property int $komisijaclan_id
 * @property int $komisijafunkcija_id
 * @property int $komisijaoblast_id
 * @property SiKomisijasaziv $siKomisijasaziv
 * @property SiKomisijaclan $siKomisijaclan
 * @property SiKomisijafunkcija $siKomisijafunkcija
 * @property SiKomisijaoblast $siKomisijaoblast
 */
class SiKomisijaKomisijaclan extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'si_komisija_komisijaclan';

    /**
     * @var array
     */
    protected $fillable = ['komisijaoblast_id'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siKomisijasaziv()
    {
        return $this->belongsTo('App\Models\SiKomisijasaziv', 'komisijasaziv_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siKomisijaclan()
    {
        return $this->belongsTo('App\Models\SiKomisijaclan', 'komisijaclan_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siKomisijafunkcija()
    {
        return $this->belongsTo('App\Models\SiKomisijafunkcija', 'komisijafunkcija_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siKomisijaoblast()
    {
        return $this->belongsTo('App\Models\SiKomisijaoblast', 'komisijaoblast_id');
    }
}
