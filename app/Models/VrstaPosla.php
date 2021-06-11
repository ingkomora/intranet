<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $naziv
 * @property string $naziv_gen
 * @property string $created_at
 * @property string $updated_at
 * @property RegOblast[] $oblasti
 */
class VrstaPosla extends Model
{
    use CrudTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vrste_poslova';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['naziv', 'naziv_gen', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function oblasti() {
        return $this->belongsToMany('App\Models\RegOblast', 'reg_oblast_vrsta_posla', 'vrsta_posla_id', 'reg_oblast_id')
            ->using('App\Models\RegOblastVrstaPosla')
            ->withPivot([
                'created_at',
                'updated_at',
            ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function podOblasti() {
        return $this->belongsToMany('App\Models\RegPodOblast', 'pod_oblast_vrsta_posla', 'vrsta_posla_id', 'pod_oblast_id')
            ->using('App\Models\PodOblastVrstaPosla')
            ->withPivot([
                'created_at',
                'updated_at',
            ]);
    }

}
