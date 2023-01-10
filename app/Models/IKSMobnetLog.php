<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $zahtev_id
 * @property string $tekst
 * @property string $datum_kreiranja
 * @property IKSMobnetZahtev $zahtev
 */
class IKSMobnetLog extends Model
{
    use CrudTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tiksmobnetlog';

    const CREATED_AT = 'datum_kreiranja';
    const UPDATED_AT = 'datum_promene';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zahtev()
    {
        return $this->belongsTo('App\Models\IKSMobnetZahtev', 'zahtev_id');
    }
}
