<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $request_category_id
 * @property integer $status_id
 * @property string $note
 * @property integer $requestable_id
 * @property string $requestable_type
 * @property string $created_at
 * @property string $updated_at
 * @property RequestCategory $requestCategory
 * @property Status $status
 * @property Document[] $documents
 * @property RequestExternal $requestable
 */
class RequestExternal extends Model
{
    use CrudTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'requests_external';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public static function existingStatuses(int $type = NULL): array
    {
        if (!is_null($type)) {
            $statusi = Status::where('id', '<>', NEAKTIVAN)
                ->whereHas('requestsExternal', function ($query) use ($type) {
                    $query->where('request_category_id', $type);
                })
                ->orderBy('id')
                ->pluck('naziv', 'id')
                ->toArray();
        } else {
            $statusi = Status::where('id', '<>', NEAKTIVAN)
                ->whereHas('requestsExternal')
                ->orderBy('id')
                ->pluck('naziv', 'id')
                ->toArray();
        }

        return $statusi;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requestCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\RequestCategory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\Status', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function documents(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function requestable(): \Illuminate\Database\Eloquent\Relations\MorphTo
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
