<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $username
 * @property string $osoba
 * @property string $roles
 * @property string $password
 * @property int $id
 * @property Osoba $osobaId
 */
class Korisnik extends Model
{
    use CrudTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tkorisnik';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    public function osobaId()
    {
        return $this->belongsTo('App\Models\Osoba', 'id', 'osoba');
    }

}
