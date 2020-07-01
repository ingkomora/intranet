<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $date
 * @property integer $userid
 * @property string $level
 * @property string $text
 */
class LogLib extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tlog_lib';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'date';

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
    protected $fillable = ['date','userid', 'level', 'text'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

}
