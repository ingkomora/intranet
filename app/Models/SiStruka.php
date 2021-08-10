<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $treg_oblast_id
 * @property string $naziv
 * @property int $zavodnibrojbrojcanik
 * @property string $zavodnibrojprefiks
 * @property string $tmp_naziv
 * @property int $tmp_zavodnibrojcanik
 * @property string $tmp_zavodnibrojprefiks
 * @property string $status
 * @property RegOblast $regOblast
 * @property SiKomisija[] $komisije
 */

class SiStruka extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tstrucniispitstruka';

    /**
     * @var array
     */
    protected $fillable = ['treg_oblast_id', 'naziv', 'zavodnibrojbrojcanik', 'zavodnibrojprefiks', 'tmp_naziv', 'status'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function regOblast()
    {
        return $this->belongsTo('App\Models\RegOblast');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function komisije()
    {
        return $this->hasMany('App\Models\SiKomisija', 'struka_id');
    }
}
