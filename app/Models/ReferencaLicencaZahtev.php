<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property integer $referenca_id
 * @property integer $licenca_zahtev_id
 * @property string $created_at
 * @property string $updated_at
 */
class ReferencaLicencaZahtev extends Pivot {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'referenca_licenca_zahtev';

    /**
     * @var array
     */
    protected $fillable = ['referenca_id', 'licenca_zahtev_id', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
//    protected $touches = ['osoba'];
}
