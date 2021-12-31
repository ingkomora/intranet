<?php

namespace App\Models;

use App\Libraries\ProveraLibrary;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Tesla\JMBG\JMBG;

/**
 * @property string $id
 * @property string $ime
 * @property string $prezime
 * @property string $titula
 * @property string $roditelj
 * @property string $rodjenjemesto
 * @property int $zvanje
 * @property string $prebivalistebroj
 * @property string $prebivalistemesto
 * @property string $prebivalistedrzava
 * @property string $prebivalisteadresa
 * @property string $kontaktetel
 * @property string $mobilnitel
 * @property string $kontaktemail
 * @property int $clan
 * @property string $lib
 * @property Zvanje $zvanjeId
 * @property Licenca[] $licence
 * @property Request[] $requests
 * @property Membership[] $memberships
 */
class Osoba extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'tosoba';
    //mora ovako jos uvek nije implementirana getRouteKeyName()
//     protected $primaryKey = 'lib';
    // public $timestamps = false;
    protected $guarded = ['id'];
//    protected $guarded = [];//PRIVREMENO ZBOG KOPIRANJA IZ OSOBASI
    // protected $fillable = [];
//     protected $hidden = [];
    // protected $dates = [];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['ime_prezime_jmbg', 'full_name', 'ime_prezime_licence', 'full_address'];

    protected $keyType = 'string';
    public $incrementing = FALSE;

    /**
     * @var array
     */

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = TRUE;

//    public $identifiableAttribute = 'ime_prezime_jmbg';
    public $identifiableAttribute = 'id';
//    public $identifiableAttribute = 'lib';

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    /*    public function getRouteKeyName()
        {
            return 'lib';
        }*/


    public function validanJmbg()
    {
        if (JMBG::for($this->id)->isValid()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function isMale()
    {
        return substr($this->id, 9, 3) < 500;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zvanjeId()
    {
        return $this->belongsTo('App\Models\Zvanje', 'zvanje');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firma()
    {
        return $this->belongsTo('App\Models\Firma', 'firma_mb', 'mb');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function opstinaId()
    {
        return $this->belongsTo('App\Models\Opstina', 'prebivalisteopstinaid')->orderBy('ime','desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function titulaId()
    {
        return $this->belongsTo('App\Models\Titula', 'titula');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function funkcijaId()
    {
        return $this->belongsTo('App\Models\Funkcija', 'funkcija');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function funkcioneri()
    {
        return $this->hasMany('App\Models\Funkcioner', 'osoba');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function licence()
    {
        return $this->hasMany('App\Models\Licenca', 'osoba');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evidencijeMirovanja()
    {
        return $this->hasMany('App\Models\Tevidencijamirovanja', 'osoba');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zahteviLicence()
    {
        return $this->hasMany('App\Models\ZahtevLicenca', 'osoba');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clanarine()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba')
            ->orderBy('rokzanaplatu');
    }


    public function prvaClanarina()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba')
            ->orderBy('rokzanaplatu')->limit(1);
    }

    public function poslednjaClanarina()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba')
            ->orderBy('rokzanaplatu','desc')->limit(1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siPrijave()
    {
        return $this->hasMany('App\Models\SiPrijava', 'osoba_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function preglediZahteva()
    {
        return $this->belongsToMany('App\Models\ZahtevLicenca', 'tzahtevpregled', 'osoba_id', 'zahtev_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function osiguranja()
    {
        return $this->belongsToMany('App\Models\Osiguranje', 'osiguranje_osoba', 'osoba_id', 'osiguranja_id')
            ->using('App\Models\OsiguranjeOsoba')
            ->withPivot([
                'datum_provere',
                'created_at',
                'updated_at',
            ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests()
    {
        return $this->hasMany('App\Models\Request', 'osoba_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function documents()
    {
        return $this->hasManyThrough('App\Models\Document', 'App\Models\Request');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function memberships()
    {
        return $this->hasMany('App\Models\Membership', 'osoba_id');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    /**
     * Get the user's Full name.
     *
     * @param string $value
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->ime} {$this->prezime}";
    }

    /**
     * Get the user's Full name with jmbg.
     *
     * @param string $value
     * @return string
     */
    public function getImePrezimeJmbgAttribute()
    {
        return "{$this->ime} {$this->prezime} ($this->id)";
    }

    /**
     * Get the user's Full name with roditelj.
     *
     * @param string $value
     * @return string
     */
    public function getImeRoditeljPrezimeAttribute()
    {
        return "{$this->ime} ($this->roditelj) {$this->prezime}";
    }

    /**
     * Get the user's Full address .
     *
     * @param string $value
     * @return string
     */
    public function getFullAddressAttribute()
    {
        return "$this->prebivalisteadresa, $this->prebivalistebroj, $this->prebivalistemesto";
    }

    /**
     * Get the user's Full name with licence.
     *
     * @param string $value
     * @return string
     */
    public function getImePrezimeLicenceAttribute()
    {
        $licenceArray = $this->licence->where('status', '<>', 'D')->pluck('id')->toArray();
        $licence = implode(', ', $licenceArray);
        return "{$this->ime} {$this->prezime} ($licence)";
    }

    /**
     * Get the user's Licence.
     *
     * @param string $value
     * @return string
     */
    public function getLicenceArrayAttribute()
    {
        $licenceArray = $this->licence->where('status', '<>', 'D')->pluck('id')->toArray();
        $licence = implode(', ', $licenceArray);
        return $licence;
    }


    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
