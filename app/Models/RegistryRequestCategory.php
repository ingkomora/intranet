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
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'registry_request_category';

    /**
     * @var array
     */
    protected $fillable = ['registry_id', 'request_category_id', 'created_at', 'updated_at'];

}
