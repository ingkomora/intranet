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
 * @property Clanarina[] $clanarine
 * @property Request[] $requests
 * @property Request[] $zahtevZaMirovanje
 * @property EvidencijaMirovanja $poslednjeMirovanje
 * @property EvidencijaMirovanja[] $aktivnaMirovanja
 */
class Membership extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;

    /**
     * @var array
     */
    protected $fillable = ['osoba_id', 'status_id', 'started_at', 'ended_at', 'note', 'created_at', 'updated_at'];

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

    public function zahtevZaMirovanje(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Request::class, 'osoba_id', 'osoba_id')
            ->where('request_category_id', 4);
    }

    public function poslednjeMirovanje()
    {
        return $this->hasMany('App\Models\EvidencijaMirovanja', 'osoba', 'osoba_id')
            ->orderByDesc('id')->first();
    }

    public function aktivnaMirovanja(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\EvidencijaMirovanja', 'osoba', 'osoba_id')
            ->whereNull('datumprestanka')
            ->whereRaw('datumkraja>=now()::date');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clanarine(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba', 'osoba_id')
            ->orderBy('rokzanaplatu');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function poslednjaClanarina(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba', 'osoba_id')
            ->orderBy('rokzanaplatu', 'desc')->limit(1);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    /**
     * Scope a query to only include active memberships.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('status_id', 10);
    }

    public function scopeActiveAndMirovanje($query)
    {
        $query->whereIn('status_id', [10, 12]);
    }

    public function scopeClan($query)
    {
        $query->whereHas('osoba', function ($q) {
            $q->whereIn('clan', [1, 10, 100]);
        });
    }

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
