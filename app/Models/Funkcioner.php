<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $osoba_id
 * @property int $mandat_id
 * @property int $funkcija_id
 * @property int $region_id
 * @property int $zvanje_grupa_id
 * @property int $status_id
 * @property string $foto
 * @property string $cv
 * @property string $napomena
 * @property Osoba $osoba
 * @property Funkcija $funkcija
 * @property FunkcionerMandat $funkcionerMandat
 * @property Region $region
 * @property Sekcija $sekcija
 * @property Status $status
 */

class Funkcioner extends Model
{
    use HasFactory;

    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'funkcioneri';
//    protected $primaryKey = ['osoba_id', 'mandat_id', 'funkcija_id', 'status_id'];
//    public $timestamps = false;
    protected $guarded = ['id'];
//    protected $fillable = [];
//    protected $hidden = [];
//    protected $dates = [];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */

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
    public function osoba()
    {
        return $this->belongsTo('App\Models\Osoba', 'osoba_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function funkcija()
    {
        return $this->belongsTo('App\Models\Funkcija', 'funkcija_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function funkcionerMandat()
    {
        return $this->belongsTo('App\Models\FunkcionerMandat', 'mandat_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo('App\Models\Region', 'region_id');
    }

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
    public function status()
    {
        return $this->belongsTo('App\Models\Status', 'status_id');
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
     * Get the user's Full name with licence.
     *
     * @param string $value
     * @return string
     */
    public function getImePrezimeLicenceAttribute()
    {
        $licenceArray = $this->osoba->licence->where('status', '<>', 'D')->pluck('id')->toArray();
        $licence = implode(', ', $licenceArray);
        return "{$this->osoba->ime} {$this->osoba->prezime} ($licence)";
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

}
