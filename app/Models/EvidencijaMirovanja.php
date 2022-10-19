<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $datumpocetka
 * @property string $osoba
 * @property string $datumkraja
 * @property string $datumprestanka
 * @property string $created_date
 * @property string $brresenja
 * @property string $brresenjaprestanka
 * @property string $licence
 * @property int $id
 * @property string $edited_date
 * @property string $razlog_mirovanja_usled
 * @property string $prilozio_dokumentaciju
 * @property string $napomena
 * @property string mirovanje_do
 * @property Osoba $osobaId
 * @property Request[] $requests
 */
class EvidencijaMirovanja extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tevidencijamirovanja';

    /**
     * The primary key of the table.
     *
     * @var string
     */
//    protected $primaryKey = array('osoba', 'datumpocetka');

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = FALSE;

    /**
     * @var array
     */
    protected $fillable = ['osoba', 'datumpocetka', 'datumkraja', 'datumprestanka', 'created_date', 'brresenja', 'brresenjaprestanka', 'licence', 'id', 'edited_date'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function osobaId()
    {
        return $this->belongsTo('App\Models\Tosoba', 'osoba');
    }

    public function requests()
    {
        return $this->morphMany(Request::class, 'requestable');
    }
}
