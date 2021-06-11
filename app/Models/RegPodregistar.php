<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $naziv
 * @property string $naziv_cir
 * @property RegSekcija[] $regSekcije
 */
class RegPodregistar extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'treg_podregistar';

    /**
     * @var array
     */
    protected $fillable = ['naziv', 'naziv_cir'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regSekcije()
    {
        return $this->hasMany('App\Models\RegSekcija', 'podregistar_id');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    /**
     * Get the zvanje id+naziv.
     *
     * @param string $value
     * @return string
     */
    public function getIdNazivAttribute() {
        return "{$this->id}-{$this->naziv}";
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
