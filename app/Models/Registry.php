<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $base_number
 * @property boolean $copy
 * @property string $subject
 * @property int $sub_base_number
 * @property int $registry_department_unit_id
 * @property int $counter
 * @property int $status_id
 * @property string $created_at
 * @property string $updated_at
 * @property RegistryDepartmentUnit $registryDepartmentUnit
 */
class Registry extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'registries';
    // protected $primaryKey = 'id';
    public $timestamps = TRUE;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

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
    public function registryDepartmentUnit()
    {
        return $this->belongsTo('App\Models\RegistryDepartmentUnit', 'registry_department_unit_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function statusi()
    {
        return $this->belongsTo('App\Models\Statusi', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function requestCategories() {
        return $this->belongsToMany('App\Models\Request', 'registry_request_category', 'registry_id', 'request_category_id')
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
     * Get the user's Document category name and registry number.
     *
     * @param string $value
     * @return string
     */
    public function getIdSubjectAttribute()
    {
        return "{$this->id} ($this->subject)";
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
