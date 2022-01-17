<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $brlicence
 * @property int $azurirao_korisnik
 * @property int $azurirao_admin
 * @property string $rokzanaplatu
 * @property float $iznoszanaplatu
 * @property float $iznosuplate
 * @property float $pretplata
 * @property string $napomena
 * @property int $potvrdaposlata
 * @property string $datumslanjapotvrde
 * @property string $datumazuriranja
 * @property string $datumazuriranja_admin
 * @property Tappkorisnik $admin
 * @property Tappkorisnik $korisnik
 * @property Licenca $licenca
 */
class ClanarinaOld extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tclanarina';

    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['brlicence', 'azurirao_korisnik', 'azurirao_admin', 'rokzanaplatu', 'iznoszanaplatu', 'iznosuplate', 'pretplata', 'napomena', 'potvrdaposlata', 'datumslanjapotvrde', 'datumazuriranja', 'datumazuriranja_admin'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo('App\Models\Tappkorisnik', 'azurirao_admin');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function korisnik()
    {
        return $this->belongsTo('App\Models\Tappkorisnik', 'azurirao_korisnik');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function licenca()
    {
        return $this->belongsTo('App\Models\Licenca', 'brlicence');
    }
}
