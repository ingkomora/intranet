<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $label
 * @property string $name
 * @property int $parent_id
 * @property string $created_at
 * @property string $updated_at
 * @property RegistryDepartmentUnit[] $childrenRegistryDepartmentUnits
 * @property RegistryDepartmentUnit $allChildrenRegistryDepartmentUnits
 */
class RegistryDepartmentUnit extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'registry_department_units';
    // protected $primaryKey = 'id';
//    public $timestamps = true;
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
    // return one level of child items
    public function childrenRegistryDepartmentUnits()
    {
        return $this->hasMany(RegistryDepartmentUnit::class, 'parent_id');
    }

    // recursive relationship
    public function allChildrenRegistryDepartmentUnits()
    {
        return $this->childrenRegistryDepartmentUnits()->with('allChildrenRegistryDepartmentUnits');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeChildless($q)
    {
        $q->has('childrenRegistryDepartmentUnits', '=', 0);
    }
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getNameLabelAttribute(): string
    {
        return "$this->name ($this->label)";
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
