<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property int $vrsta_licence_id
 * @property string $naziv
 * @property string $naziv_cir
 * @property string $status
 * @property int $podoblast_id
 * @property LicencaVrsta $vrstaLicence
 */
class RegLicencaTip extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'treg_licencatip';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['vrsta_licence_id', 'naziv', 'naziv_cir', 'status', 'podoblast_id'];

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
        return $this->belongsTo('App\Models\LicencaVrsta', 'vrsta_licence_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function podOblast()
    {
        return $this->belongsTo('App\Models\RegPodoblast', 'podoblast_id');
    }
}
