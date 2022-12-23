<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Zvanje extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'tzvanje';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    /**
     * @var array
     */
    protected $fillable = ['zvanje_grupa_id', 'naziv', 'naziven', 'skrnaziven', 'skrnaziv', 'reg_sekcija_id'];
    // protected $hidden = [];
    // protected $dates = [];
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sekcija()
    {
        return $this->belongsTo('App\Models\Sekcija', 'zvanje_grupa_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function regSekcija()
    {
        return $this->belongsTo('App\Models\RegSekcija', 'reg_sekcija_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function osobe()
    {
        return $this->hasMany('App\Models\Osoba', 'zvanje');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tclanovikomisijeuslovnazapisniks()
    {
        return $this->hasMany('App\Models\Tclanovikomisijeuslovnazapisnik', 'zvanje_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tclanovikomisijezapisniks()
    {
        return $this->hasMany('App\Models\Tclanovikomisijezapisnik', 'zvanje_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tizboriregion2016glasackispisakizvodjacis()
    {
        return $this->hasMany('App\Models\Tizboriregion2016glasackispisakizvodjaci', 'zvanje_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tizboriregion2016glasackispisakprojektantis()
    {
        return $this->hasMany('App\Models\Tizboriregion2016glasackispisakprojektanti', 'zvanje_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tizboriregion2016glasackispisakurbanistis()
    {
        return $this->hasMany('App\Models\Tizboriregion2016glasackispisakurbanisti', 'zvanje_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tizboriskupstina2016BirackiSpisaks()
    {
        return $this->hasMany('App\Models\Tizboriskupstina2016BirackiSpisak', 'zvanje');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tizboriskupstinaArhivaBirackiSpisaks()
    {
        return $this->hasMany('App\Models\TizboriskupstinaArhivaBirackiSpisak', 'zvanje');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function oblast()
    {
        return $this->belongsTo(RegOblast::class, 'reg_oblast_id');
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
