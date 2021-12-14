<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $naziv
 * @property string $naziv_full
 * @property string $naziv_cir
 * @property string $naziv_full_cir
 * @property string $trajanje
 * @property string $napomena
 * @property FunkcionerMandat $mandati
 */

class FunkcionerMandatTip extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'funkcioneri_mandati_tipovi';

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
    public function mandati()
    {
        return $this->hasMany('App\Models\FunkcionerMandat', 'mandat_tip_id');
    }
}
