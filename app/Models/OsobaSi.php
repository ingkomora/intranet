<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Tesla\JMBG\JMBG;

class OsobaSi extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'tosobasi';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    public $identifiableAttribute = 'id';


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function validanJmbg(){
        if(JMBG::for($this->id)->isValid()){
            return true;
        }else {
            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zvanjeId()
    {
        return $this->belongsTo('App\Models\Zvanje', 'zvanje');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function opstinaId()
    {
        return $this->belongsTo('App\Models\Opstina', 'prebivalisteopstinaid');
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
     * Get the user's Full name.
     *
     * @param string $value
     * @return string
     */
    public function getFullNameAttribute() {
        return "{$this->ime} {$this->prezime}";
    }

    /**
     * Get the user's Full name with jmbg.
     *
     * @param string $value
     * @return string
     */
    public function getImePrezimeJmbgAttribute() {
        return "{$this->ime} {$this->prezime} ($this->id)";
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
