<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $sekcija
 * @property string $naziv
 * @property VrstaPosla $vrstaPosla
 * @property Referenca[] $reference
 */
class VrstaPlana extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tvrstaplana';

    /**
     * @var array
     */
    protected $fillable = ['sekcija', 'naziv'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vrstaPosla()
    {
        return $this->belongsTo('App\Models\VrstaPosla', 'sekcija');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reference()
    {
        return $this->hasMany('App\Models\Referenca', 'vrstaplana');
    }
}
