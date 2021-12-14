<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\DocBlock\Tags\Method;

/**
 * @property int $id
 * @property string $naziv
 * @property string $naziv_full
 * @property string $naziv_cir
 * @property string $naziv_full_cir
 * @property string $datum_od
 * @property string $datum_do
 * @property int $mandat_tip_id
 * @property int $status_id
 * @property string $napomena
 * @property FunkcionerMandatTip $mandatTip
 * @property Funkcioner $funkcioneri
 */
class FunkcionerMandat extends Model
{
    use HasFactory;

    use CrudTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'funkcioneri_mandati';

    /**
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function funkcionerMandatTip()
    {
        return $this->belongsTo('App\Models\FunkcionerMandatTip', 'mandat_tip_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Status', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function funkcioneri()
    {
        return $this->hasMany('App\Models\Funkcioner', 'mandat_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aktivniFunkcioneri()
    {
        return $this
            ->hasMany('App\Models\Funkcioner', 'mandat_id')
            ->where('status_id', AKTIVAN);
    }

    /**
     * Accessor.
     *
     * @param string $value
     * @return string
     */
    public function getNazivDatumOdAttribute()
    {
        return "{$this->naziv} ($this->datum_od)";
    }

}
