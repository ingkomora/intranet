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
 * @property Request $requestable
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
//    public $timestamps = FALSE; //PRIVEREMENO ZBOG KOPIRANJA
    protected $guarded = ['id'];
//    protected $guarded = [];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
//    public $identifiableAttribute = 'id';


    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public static function existingStatuses(int $type = NULL) : array
    {
        if (!is_null($type)) {
            $statusi = Status::where('id', '<>', NEAKTIVAN)
                ->whereHas('requests', function ($query) use ($type) {
                $query->where('request_category_id', $type);
            })
                ->orderBy('id')
                ->pluck('naziv', 'id')
                ->toArray();
        } else {
            $statusi = Status::where('id', '<>', NEAKTIVAN)
                ->whereHas('requests')
                ->orderBy('id')
                ->pluck('naziv', 'id')
                ->toArray();
        }

        return $statusi;
    }

    public function opstiStatuses()
    {
        return $this->status()->where('log_status_grupa_id', OPSTA)->get();
    }
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
        return $this->belongsTo('App\Models\Status', 'status_id')//            ->where('log_status_grupa_id', OPSTA)
            ;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clanarine()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba', 'osoba_id')
            ->orderBy('rokzanaplatu');
    }

    /**
     * Get all of the request's documents.
     */
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Get the parent commentable model (post or video).
     */
    public function requestable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function registries()
    {
        return $this->belongsToMany('App\Models\Registry', 'registry_request_category', 'request_category_id', 'registry_id')
            ->using('App\Models\RegistryRequestCategory')
            ->withPivot([
                'created_at',
                'updated_at',
            ]);
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
