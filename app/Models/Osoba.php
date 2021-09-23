<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Tesla\JMBG\JMBG;

/**
 * @property string $id
 * @property string $ime
 * @property string $prezime
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
    // protected $primaryKey = 'id';
    // public $timestamps = false;
//    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['ime_prezime_jmbg', 'full_name', 'ime_prezime_licence'];

    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['id', 'zvanje', 'titula', 'funkcija', 'ime', 'prezime', 'roditelj', 'devojackoprezime', 'rodjenjemesto', 'rodjenjeopstina', 'rodjenjedrzava', 'prebivalistebroj', 'prebivalistemesto', 'prebivalisteopstina', 'prebivalisteadresa', 'kontakttel', 'mobilnitel', 'kontaktemail', 'firmanaziv', 'firmamesto', 'firmaopstina', 'firmaweb', 'firmatel', 'firmaemail', 'diplfakultet', 'diplmesto', 'dipldrzava', 'diplodsek', 'diplsmer', 'diplgodina', 'mrfakultet', 'mrmesto', 'mrdrzava', 'mrodsek', 'mrsmer', 'mrgodina', 'drfakultet', 'drmesto', 'drdrzava', 'drodsek', 'drsmer', 'drgodina', 'lozinka', 'biografija', 'diplbroj', 'mrbroj', 'drbroj', 'pol', 'rodjenjedan', 'rodjenjemesec', 'rodjenjegodina', 'rodjenjeopstinaid', 'rodjenjeinodrzava', 'rodjenjeinomesto', 'diplfakultetid', 'diplsmerid', 'diplunetfakultet', 'diplunetsmer', 'specfakultetid', 'specunetfakultet', 'specsmerid', 'specunetsmer', 'specgodina', 'magfakultetid', 'magunetfakultet', 'magsmerid', 'magunetsmer', 'docfakultetid', 'docunetfakultet', 'docsmerid', 'docunetsmer', 'prebivalisteopstinaid', 'kontaktfax', 'licniweb', 'adresaprikazi', 'telefonprikazi', 'mobilniprikazi', 'faxprikazi', 'mailprikazi', 'prikazisliku', 'firmaopstinaid', 'firmafax', 'imalp', 'zaposlen', 'st_drzavljanstvoscg', 'clanskupstine', 'dozvolareklamnimail', 'lib', 'temp_dms_password', 'prezime_staro', 'primary_serial', 'ranije_deaktivirao_clanstvo', 'clan', 'datumrodjenja', 'prebivalistedrzava', 'vrsta_poslova', 'godine_radnog_iskustva', 'bolonja', 'firma_mb', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

//    public $identifiableAttribute = 'ime_prezime_jmbg';
    public $identifiableAttribute = 'id';

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function validanJmbg()
    {
        if (JMBG::for($this->id)->isValid()) {
            return true;
        } else {
            return false;
        }
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
        return $this->belongsTo('App\Models\Opstina', 'prebivalisteopstinaid');
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
    public function getImePrezimeRoditeljAttribute()
    {
        return "{$this->ime} {$this->prezime} ($this->roditelj)";
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
