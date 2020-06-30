<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $naziv
 * @property string $created_at
 * @property string $updated_at
 * @property Osiguranje[] $osiguranja
 */
class OsiguranjePolisaPokrice extends Model
{
    use CrudTrait;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'osiguranja_polise_pokrica';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['naziv', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    public $identifiableAttribute = 'naziv';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function osiguranja()
    {
        return $this->hasMany('App\Models\Osiguranja', 'polisa_pokrice_id');
    }
}
