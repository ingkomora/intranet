<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property integer $osiguranja_id
 * @property string $osoba_id
 * @property string $datum_provere,
 * @property string $created_at
 * @property string $updated_at
 */
class OsiguranjeOsoba extends Pivot {

    use CrudTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'osiguranje_osoba';

    /**
     * @var array
     */
    protected $fillable = ['osiguranja_id', 'osoba_id', 'datum_provere', 'created_at', 'updated_at'];

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
