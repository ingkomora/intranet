<?php

namespace App\Models;

use App\Libraries\ProveraLibrary;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Tesla\JMBG\JMBG;

/**
 * @property string $id
 * @property int idn
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
 * @property int $prebivalisteopstinaid
 * @property string $lib
 * @property Zvanje $zvanjeId
 * @property Firma $firma
 * @property Titula $titulaId
 * @property Licenca[] $licence
 * @property Request[] $requests
 * @property Membership[] $memberships
 * @property Osiguranje[] $osiguranja
 * @property Osiguranje[] $aktivnaOsiguranja
 * @property Clanarina $prvaClanarina
 * @property Clanarina $poslednjaPlacenaClanarina
 * @property Clanarina $poslednjaClanarina
 * @property Clanarina $izmirenaClanarina
 * @property string ulica
 * @property string broj
 * @property string podbroj
 * @property string sprat
 * @property string stan
 * @property int posta_opstina_id
 * @property string posta_pb
 * @property string posta_drzava
 * // * @property int $member
 * // * @property int $notMember
 * // * @property int $memberOnHold
 * // * @property int $memberToDelete
 * @property Korisnik $korisnik
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
//    public $identifiableAttribute = 'ime_prezime_jmbg';
    public $identifiableAttribute = 'id';
//    public $identifiableAttribute = 'lib';
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['ime_prezime_jmbg', 'full_name', 'ime_prezime_licence', 'full_address'];

    protected $keyType = 'string';
    public $incrementing = FALSE;

    private $prebivaliste_array = ['prebivalisteadresa' => '', 'prebivalistebroj' => '', 'prebivalisteopstinaid' => '', 'prebivalistedrzava' => '', 'prebivalistemesto' => ''];
    private $posta_array = ['ulica' => '', 'broj' => '', 'podbroj' => ''/*, 'sprat' => '', 'stan' => ''*/, 'posta_opstina_id' => '', 'posta_pb' => '', 'posta_drzava' => ''];
    private $osnovne_strudije_array = ['diplfakultet' => '', 'diplmesto' => '', 'diplgodina' => '', 'diplbroj' => ''];
    private $master_strudije_array = ['mrfakultet' => '', 'mrmesto' => '', 'mrgodina' => '', 'mrbroj' => ''];


//TODO da li ovo ovako treba
    /*    public $member = MEMBER;
        public $notMember = NOT_MEMBER;
        public $memberToDelete = MEMBER_TO_DELETE;
        public $memberOnHold = MEMBER_ON_HOLD;*/

    /**
     * @var array
     */

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = TRUE;


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

    private function getOpstina($opstina_id)
    {
        return Opstina::find($opstina_id);
    }

    public function getOsiguranjaData()
    {
        $osiguranja = $this->osiguranja;

        $results = $osiguranja->map(function ($osiguranje, $value) {
            return $osiguranje->osiguranje_data;
        });
//        dd($results);
        $result = '';
        $counter = 0;
        foreach ($results as $string) {
            $result .= $counter === 0 ? "$string" : "<br>$string";
            $counter++;
        }
        return $result;
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
        return $this->belongsTo('App\Models\Opstina', 'prebivalisteopstinaid')->orderBy('ime', 'desc');
    }

    public function postaOpstinaId()
    {
        return $this->belongsTo('App\Models\Opstina', 'posta_opstina_id')->orderBy('ime', 'desc');
    }

    public function korisnik()
    {
        return $this->hasOne('App\Models\Korisnik', 'osoba', 'id');
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
    public function funkcioneri(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\Funkcioner', 'osoba_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aktivniClanoviVeca()
    {
        return $this->hasMany('App\Models\Funkcioner', 'osoba_id')
            ->whereHas('funkcionerMandat', function ($q) {
                $q->where('mandat_tip_id', 6);
            })
            ->where('status_id', 1);
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
        return $this->hasMany('App\Models\EvidencijaMirovanja', 'osoba');
    }

    public function aktivnaMirovanja(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\EvidencijaMirovanja', 'osoba')
            ->whereNull('datumprestanka')
            ->whereRaw('datumkraja>=now()::date');
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
            ->orderBy('rokzanaplatu', 'desc')->limit(1);
    }

    public function poslednjaPlacenaClanarina()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba')
            ->orderBy('rokzanaplatu', 'desc')->whereRaw('iznoszanaplatu = iznosuplate + pretplata')->limit(1);
    }

    public function poslednjaPlacenaClanarinaDatumUplate()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba')
            ->orderBy('rokzanaplatu', 'desc')->whereRaw('iznoszanaplatu = iznosuplate + pretplata')->whereNotNull('datumuplate')->where('datumuplate', '<', '2021-11-04')->limit(1);
    }

    public function clanarinaDatumAzuriranjaAdmin($year)
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba')
            ->orderBy('rokzanaplatu', 'desc')->whereRaw('iznoszanaplatu = iznosuplate + pretplata')->whereYear('datumazuriranja_admin', $year)->whereNull('napomena');
    }

    public function izmirenaClanarina()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba')
            ->orderBy('rokzanaplatu', 'desc')->whereRaw('rokzanaplatu >= now()')->limit(1);
    }

    public function neIzmirenaClanarinaDo2021()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba')->distinct('osoba')
            ->orderBy('rokzanaplatu', 'desc')->whereRaw('iznoszanaplatu > iznosuplate + pretplata')->whereDate('rokzanaplatu', '<', '2021-01-01');
    }

    public function neIzmirenaClanarinaDo2022()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba')->distinct('osoba')
            ->orderBy('rokzanaplatu', 'desc')->whereRaw('iznoszanaplatu > iznosuplate + pretplata')->whereDate('rokzanaplatu', '<', '2022-01-01');
    }

    public function dugujeClanarinuZa2021()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba')->distinct('osoba')
            ->orderBy('rokzanaplatu', 'desc')->whereRaw('iznoszanaplatu > iznosuplate + pretplata')->whereDate('rokzanaplatu', '>', '2021-01-01')->whereDate('rokzanaplatu', '<', '2022-01-01');
    }

    public function poslednjeDveClanarine()
    {
        return $this->hasMany('App\Models\Clanarina', 'osoba')
            ->orderBy('rokzanaplatu', 'desc')->limit(2);
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
            ])
            ->orderByDesc('polisa_datum_pocetka')
            ;
    }

    public function aktivnaOsiguranja()
    {
        return $this->belongsToMany('App\Models\Osiguranje', 'osiguranje_osoba', 'osoba_id', 'osiguranja_id')
            ->where('status_polise_id', 1)
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

    public function getImePrezimeLibAttribute()
    {
        return "{$this->ime} {$this->prezime} ($this->lib)";
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
        return implode(', ', $licenceArray);
    }

    public function getDataLicenceToArrayAttribute(): array
    {
        $licence = Licenca::where('osoba', $this->id)
            ->where('status', '<>', 'D')
            ->get(['id', 'datumuo', 'licencatip', 'zahtev', 'status']);
        $licenca_data = [];
        foreach ($licence as $key => $licenca) {
            $licenca_data[$key] = "$licenca->id ({$licenca->tipLicence->oznaka} - {$licenca->licencatip}) od " . Carbon::parse($licenca->datumuo)->format('d.m.Y');
        }
        return $licenca_data;
    }

    public function getDataPrebivalisteToStringAttribute(): string
    {
        foreach ($this->prebivaliste_array as $key => $value) {
            if (strstr($key, 'opstina')) {
                if (!empty($this->$key)) $this->prebivaliste_array['prebivalisteopstina'] = $this->getOpstina($this->$key)->ime;
                else unset($this->prebivaliste_array[$key]);
            } else {
                if (!empty($this->$key)) $this->prebivaliste_array[$key] = $this->$key;
                else unset($this->prebivaliste_array[$key]);
            }
        }

        return implode(', ', $this->prebivaliste_array);
    }

    public function getDataPostaToStringAttribute(): string
    {
        foreach ($this->posta_array as $key => $value) {
            if (strstr($key, 'opstina')) {
                if (!empty($value)) $this->posta_array['prebivalisteopstina'] = $this->getOpstina($this->$key)->ime;
                else unset($this->posta_array[$key]);
            } else {
                if (!empty($value)) $this->posta_array[$key] = $this->$key;
                else unset($this->posta_array[$key]);
            }
        }
        return implode(', ', $this->posta_array);
    }

    public function getDataMasterStudijeToStringAttribute(): string
    {
        foreach ($this->master_strudije_array as $key => $value) {
            if (!empty($this->$key)) $this->master_strudije_array[$key] = $this->$key;
            else unset($this->master_strudije_array[$key]);
        }
        return implode(', ', $this->master_strudije_array);
    }

    public function getDataOsnovneStudijeToStringAttribute(): string
    {
        foreach ($this->osnovne_strudije_array as $key => $value) {
            if (!empty($this->$key)) $this->osnovne_strudije_array[$key] = $this->$key;
            else unset($this->osnovne_strudije_array[$key]);
        }
        return implode(', ', $this->osnovne_strudije_array);
    }

    public function getSpojenDatumRodjenjaAttribute()
    {
        if (empty($this->rodjenjedan) and empty($this->rodjenjemesec) and empty($this->rodjenjegodina)) {
            $result = '-';
        } else {
            $result = "$this->rodjenjedan.$this->rodjenjemesec.$this->rodjenjegodina";
        }
        return $result;
    }

    public function getDatumRodjenja()
    {
        return !empty($this->datumrodjenja) ? Carbon::parse($this->datumrodjenja)->format('d.m.Y') : '-';
    }


    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
