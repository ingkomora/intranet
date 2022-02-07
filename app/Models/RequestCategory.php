<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $request_category_type_id
 * @property integer $status_id
 * @property string $name
 * @property string $note
 * @property string $created_at
 * @property string $updated_at
 * @property RequestCategoryType $requestCategoryType
 * @property Status $status
 * @property Request[] $requests
 */
class RequestCategory extends Model
{
    use CrudTrait;


    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
//    public $identifiableAttribute = 'id';

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
    public function requestCategoryType()
    {
        return $this->belongsTo('App\Models\RequestCategoryType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Status', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests()
    {
        return $this->hasMany('App\Models\Request');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function registries()
    {
        return $this->belongsToMany('App\Models\Registry', 'registry_request_category', 'request_category_id', 'registry_id')
            ->using('App\Models\RegistryRequestCategory')
            ->withPivot([
                'document_category_id',
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
    /**
     * Get the Registry category
     *
     * @param string $value
     * @return string
     */
/*    public function getRegistryCategoryDocumentAttribute()
    {
        return "$this->, $this->prebivalistebroj, $this->prebivalistemesto";
    }*/
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
