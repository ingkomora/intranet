<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $naziv
 * @property string $tekst
 * @property string $napomena
 * @property string $stateable_type
 * @property integer $stateable_id
 * @property string $created_at
 * @property string $updated_at
 */
class Status extends Model
{
    use CrudTrait;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'statusi';

    /**
     * @var array
     */
    protected $fillable = ['naziv', 'tekst', 'napomena', 'stateable_type', 'stateable_id', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\morphOne
     */
    public function log()
    {
        return $this->morphOne('App\Models\Log', 'loggable');
    }

}
