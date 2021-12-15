<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $document_category_id
 * @property integer $document_type_id
 * @property integer $registry_id
 * @property integer $status_id
 * @property integer $user_id
 * @property string $registry_number
 * @property string $registry_date
 * @property string $path
 * @property string $location
 * @property string $barcode
 * @property string $metadata
 * @property string $note
 * @property string $documentable_type
 * @property integer $documentable_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $sent_at
 * @property string $valid_from
 * @property DocumentCategory $documentCategory
 * @property DocumentType $documentType
 * @property Registry $registry
 * @property Status $status
 * @property User $user
 * @property Document $documentable
 *
 */
class Document extends Model
{
    use CrudTrait;
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'documents';
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
    public function documentCategory()
    {
        return $this->belongsTo('App\Models\DocumentCategory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function documentType()
    {
        return $this->belongsTo('App\Models\DocumentType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function registry()
    {
        return $this->belongsTo('App\Models\Registry');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')
            ->where('log_status_grupa_id', DOKUMENTA);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    /**
     * Get the parent commentable model (post or video).
     */
    public function documentable()
    {
        return $this->morphTo();
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
