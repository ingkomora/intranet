<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property int $log_status_grupa_id
 * @property string $naziv
 * @property string $napomena
 * @property string $loggable_type
 * @property string $loggable_id
 * @property string $created_at
 * @property string $updated_at
 * @property LogGrupa $logGrupa
 */
class LogOsoba extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'logovi_osoba';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['log_status_grupa_id', 'naziv', 'napomena', 'loggable_type', 'loggable_id', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function logStatusGrupa()
    {
        return $this->belongsTo('App\Models\LogGrupa', 'log_status_grupa_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\morphTo
     */
    public function loggable()
    {
        return $this->morphTo();
    }
}
