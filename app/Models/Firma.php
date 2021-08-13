<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $mb
 * @property integer $pib
 * @property string $naziv
 * @property string $drzava
 * @property string $mesto
 * @property int $pb
 * @property string $adresa
 * @property int $opstina_id
 * @property string $fax
 * @property string $telefon
 * @property string $email
 * @property string $web
 * @property string $created_at
 * @property string $updated_at
 * @property Opstina $opstina
 * @property Osiguranje[] $osiguravajucaKuca
 * @property Osiguranje[] $ugovaracOsiguranja
 */
class Firma extends Model {
    use CrudTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'firme';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['naziv_mb'];

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['mb', 'pib', 'naziv', 'drzava', 'mesto', 'pb', 'adresa', 'opstina_id', 'fax', 'telefon', 'email', 'web', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public $identifiableAttribute = 'naziv';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function opstina()
    {
        return $this->belongsTo('App\Models\Opstina', 'opstina_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function osiguravajucaKuca() {
        return $this->hasMany('App\Models\Osiguranje', 'osiguravajuca_kuca_mb', 'mb');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ugovaracOsiguranja() {
        return $this->hasMany('App\Models\Osiguranje', 'ugovarac_osiguranja_mb', 'mb');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function osobe() {
        return $this->hasMany('App\Models\Osoba', 'firma_mb', 'mb');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the firma's Full name with mb.
     *
     * @param string $value
     * @return string
     */
    public function getNazivMbAttribute() {
        return "{$this->naziv} ($this->mb)";
    }


}
