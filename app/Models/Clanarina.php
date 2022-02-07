<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $osoba
 * @property string $rokzanaplatu
 * @property int $azurirao_korisnik
 * @property int $azurirao_admin
 * @property float $iznoszanaplatu
 * @property float $iznosuplate
 * @property float $pretplata
 * @property string $napomena
 * @property string $datumazuriranja
 * @property string $datumazuriranja_admin
 * @property string $datumuplate
 * @property Appkorisnik $appAdmin
 * @property AppKorisnik $appKorisnik
 * @property Osoba $osobaId
 */
class Clanarina extends Model
{
    use CrudTrait;
//    use Traits\HasCompositePrimaryKey; // *** THIS!!! ***
//    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'datumazuriranja';

    protected $table = 'tclanarinaod2006';

//    protected $primaryKey = array('osoba', 'rokzanaplatu'); // sa ovim ne radi crudcontroller

//    protected $keyType = 'array';

    public $incrementing = false;

    protected $fillable = ['osoba','rokzanaplatu','azurirao_korisnik', 'azurirao_admin', 'iznoszanaplatu', 'iznosuplate', 'pretplata', 'napomena', 'datumazuriranja', 'datumazuriranja_admin', 'datumuplate', 'created_at'];

    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function appAdmin()
    {
        return $this->belongsTo('App\Models\AppKorisnik', 'azurirao_admin');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function appKorisnik()
    {
        return $this->belongsTo('App\Models\AppKorisnik', 'azurirao_korisnik');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function osobaId()
    {
        return $this->belongsTo('App\Models\Osoba', 'osoba');
    }
}
