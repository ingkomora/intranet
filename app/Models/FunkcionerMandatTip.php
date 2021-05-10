<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $naziv
 * @property string $naziv_skr
 * @property string $naziv_cir
 * @property string $naziv_skr_cir
 * @property int $trajanje
 * @property string $napomena
 * @property FunkcionerMandat[] $mandati
 */
class FunkcionerMandatTip extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tfunkcioner_mandat_tip';

    /**
     * @var array
     */
    protected $fillable = ['naziv', 'naziv_skr', 'naziv_cir', 'naziv_skr_cir', 'trajanje', 'napomena'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mandati()
    {
        return $this->hasMany('App\Models\FunkcionerMandat', 'mandat_tip_id');
    }
}
