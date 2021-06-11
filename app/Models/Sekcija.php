<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $naziv
 * @property string $naziv_cir
 * @property string $naziv_skr
 * @property int $podregistar_id
 * @property string $naziv_padez
 * @property Funkcioner[] $funkcioneri
 * @property Zvanje[] $zvanja
 */
class Sekcija extends Model {
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
    protected $table = 'tzvanje_grupa';

    /**
     * @var array
     */
    protected $fillable = ['naziv', 'naziv_cir', 'naziv_skr', 'podregistar_id', 'naziv_padez'];

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
    public function funkcioneri() {
        return $this->hasMany('App\Models\Funkcioner', 'sekcija_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zvanja() {
        return $this->hasMany('App\Models\Zvanje', 'zvanje_grupa_id');
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
