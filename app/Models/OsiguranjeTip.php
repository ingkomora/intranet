<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $naziv
 * @property string $opis
 * @property string $created_at
 * @property string $updated_at
 * @property Osiguranja[] $osiguranjas
 */
class OsiguranjeTip extends Model
{
    use CrudTrait;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'osiguranje_tip';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['naziv', 'opis', 'created_at', 'updated_at'];

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
        return $this->hasMany('App\Models\Osiguranja');
    }
}
