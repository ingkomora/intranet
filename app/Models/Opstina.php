<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $region_id
 * @property string $ime
 * @property int $sifrasz
 * @property int $region_id_old
 * @property string $path
 * @property Region $region
 * @property Fakultet[] $fakulteti
 * @property Mesto[] $mesta
 */
class Opstina extends Model
{
    use CrudTrait;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'topstina';

    /**
     * @var array
     */
    protected $fillable = ['region_id', 'ime', 'sifrasz', 'region_id_old', 'path'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo('App\Models\Region', 'region_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fakulteti()
    {
        return $this->hasMany('App\Models\Fakultet', 'opstinaid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mesta()
    {
        return $this->hasMany('App\Models\Mesto', 'id_opstina');
    }
}
