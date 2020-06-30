<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property float $id
 * @property int $sekcija
 * @property string $naziv
 * @property string $status
 * @property string $idn
 * @property LicencaVrsta $vrstaLicence
 * @property Zahtev[] $zahtevi
 */
class LicencaTip extends Model
{
    use CrudTrait;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tlicencatip';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'float';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['sekcija', 'naziv', 'status', 'idn'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vrstaLicence()
    {
        return $this->belongsTo('App\Models\LicencaVrsta', 'sekcija');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zahtevi()
    {
        return $this->hasMany('App\Models\Zahtev', 'licencatip');
    }
}
