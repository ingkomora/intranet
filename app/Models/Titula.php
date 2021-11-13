<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $naziv
 * @property Osoba[] $osobe
 */
class Titula extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ttitula';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function osobe()
    {
        return $this->hasMany('App\Models\Osoba', 'titula');
    }
}
