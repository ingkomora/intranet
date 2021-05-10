<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $mandat_tip_id
 * @property string $naziv
 * @property string $naziv_cir
 * @property string $datum_od
 * @property string $datum_do
 * @property string $napomena
 * @property FunkcionerMandatTip $funkcionerMandatTip
 * @property Funkcioner[] $funkcioneri
 */
class FunkcionerMandat extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tfunkcioner_mandat';

    /**
     * @var array
     */
    protected $fillable = ['mandat_tip_id', 'naziv', 'naziv_cir', 'datum_od', 'datum_do', 'napomena'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function funkcionerMandatTip()
    {
        return $this->belongsTo('App\Models\FunkcionerMandatTip', 'mandat_tip_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function funkcioneri()
    {
        return $this->hasMany('App\Models\Funkcioner', 'mandat_id');
    }
}
