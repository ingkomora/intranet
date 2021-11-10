<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $naziv
 * @property string $naziv_cir
 * @property string $path
 * @property string $code
 * @property Funkcioner[] $funkcioneri
 * @property Opstina[] $opstine
 * @property Kurs[] $kursevi
 */
class Region extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'tregion';

    /**
     * @var array
     */
    protected $fillable = ['naziv', 'naziv_cir', 'path', 'code'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function funkcioneri()
    {
        return $this->hasMany('App\Models\Funkcioner', 'region_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function opstine()
    {
        return $this->hasMany('App\Models\Opstina', 'region_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kursevi()
    {
        return $this->hasMany('App\Models\Kurs', 'region');
    }
}
