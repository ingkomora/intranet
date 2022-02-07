<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property integer $registry_id
 * @property integer $request_category_id
 * @property string $created_at
 * @property string $updated_at
 */
class RegistryRequestCategory extends Pivot
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'registry_request_category';

    /**
     * @var array
     */
    protected $fillable = ['registry_id', 'request_category_id', 'document_category_id', 'created_at', 'updated_at'];

    public function registry(){
        return $this->hasOne('App\Models\Registry', 'id', 'registry_id');
    }

    public function requestCategory(){
        return $this->hasOne('App\Models\RequestCategory', 'id', 'request_category_id');
    }
}
