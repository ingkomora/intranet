<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $broj_id
 * @property int $tip_id
 * @property int $status_id
 * @property string $tekst
 * @property string $napomena
 * @property int $brojeva
 * @property string $kontakt
 * @property string $datum_kreiranja
 * @property string $datum_promene
 * @property IKSMobnetBroj $brojevi
 * @property IKSMobnetZahtevStatus $status
 * @property IKSMobnetZahtevTip $tip
 * @property IKSMobnetlog[] $logs
 */


class IKSMobnetZahtev extends Model
{
    use CrudTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tiksmobnetzahtev';


    const CREATED_AT = 'datum_kreiranja';
    const UPDATED_AT = 'datum_promene';

    /**
     * @var array
     */
    protected $guarded = ['id'];


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
    public function brojevi()
    {
        return $this->belongsTo('App\Models\IKSMobnetBroj', 'broj_id', 'broj');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\IKSMobnetZahtevStatus', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tip()
    {
        return $this->belongsTo('App\Models\IKSMobnetZahtevTip', 'tip_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany('App\Models\IKSMobnetLog', 'zahtev_id');
    }

    public function requests(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(RequestExternal::class, 'requestable');
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
