<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $ddmm
 * @property integer $sss
 */
class Lib extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tlib';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'ddmm';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['ddmm','sss'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

}
