<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $osoba_id
 * @property integer $request_category_id
 * @property integer $status_id
 * @property string $name
 * @property string $note
 * @property string $created_at
 * @property string $updated_at
 * @property Osoba $osoba
 * @property RequestCategory $requestCategory
 * @property Status $status
 * @property Clanarina[] $clanarine
 */
class Request extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'requests';
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
        return $this->belongsTo('App\Models\Osoba', 'osoba_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requestCategory()
    {
        return $this->belongsTo('App\Models\RequestCategory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Status', 'status_id')
            ->where('log_status_grupa_id', OPSTA);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public static function existingStatuses()
    {
        $statusi = Status::where('id', '<>', NEAKTIVAN)->whereHas('requests')->pluck('naziv', 'id')->toArray();

        return $statusi;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clanarine()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba', 'osoba_id')
            ->orderBy('rokzanaplatu');
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
