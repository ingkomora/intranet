<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $osoba_id
 * @property integer $status_id
 * @property string $started_at
 * @property string $ended_at
 * @property string $note
 * @property string $created_at
 * @property string $updated_at
 * @property Status $status
 * @property Osoba $osoba
 */
class Membership extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;

    /**
     * @var array
     */
    protected $fillable = ['osoba_id', 'status_id', 'started_at', 'ended_at', 'note', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Status', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function osoba()
    {
        return $this->belongsTo('App\Models\Osoba', 'osoba_id');
    }

    /**
     * Get all of the post's comments.
     */
    public function requests()
    {
        return $this->morphMany(Request::class, 'requestable');
    }
}
