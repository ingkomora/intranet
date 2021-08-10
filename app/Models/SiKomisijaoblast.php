<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $komisija_id
 * @property string $naziv
 * @property SiKomisija $siKomisija
 * @property SiKomisijaKomisijaclan[] $siKomisijaClanoviKomisije
 */
class SiKomisijaoblast extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'si_komisijaoblast';

    /**
     * @var array
     */
    protected $fillable = ['komisija_id', 'naziv'];

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
        return $this->hasMany('App\Models\SiKomisijaKomisijaclan', 'komisijaoblast_id');
    }
}
