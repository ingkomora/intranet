<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $vrsta_posla_id
 * @property string $naziv
 * @property VrstaPosla $vrstaPosla
 * @property Referenca[] $reference
 */
class Uloga extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'uloge';

    /**
     * @var array
     */
    protected $fillable = ['vrsta_posla_id', 'naziv'];

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
        return $this->belongsTo('App\Models\VrstaPosla', 'vrsta_posla_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reference()
    {
        return $this->hasMany('App\Models\Referenca', 'uloga');
    }
}
