<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $naziv
 * @property string $naziv_genitiv
 * @property RegLicencaTip[] $regTipoviLicenci
 * @property Licencatip[] $tipoviLicenci
 */
class LicencaVrsta extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tsekcija';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regTipoviLicenci()
    {
        return $this->hasMany('App\Models\RegLicencaTip', 'vrsta_licence_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tipoviLicenci()
    {
        return $this->hasMany('App\Models\Licencatip', 'sekcija');
    }
}
