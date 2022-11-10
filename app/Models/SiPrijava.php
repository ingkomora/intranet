<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $osoba_id
 * @property int $vrsta_posla_id
 * @property int $reg_oblast_id
 * @property int $reg_pod_oblast_id
 * @property int $zvanje_id
 * @property int $si_vrsta_id
 * @property int $status_prijave
 * @property string $datum_prijema
 * @property int $app_korisnik_id
 * @property string $zavodni_broj
 * @property int $strucni_rad
 * @property string $tema
 * @property string $barcode
 * @property string $created_at
 * @property string $updated_at
 * @property Osoba $osoba
 * @property Zvanje $zvanje
 * @property VrstaPosla $vrstaPosla
 * @property RegOblast $regOblast
 * @property RegPodoblast $regPodoblast
 * @property SiVrsta $siVrsta
 * @property ZahtevLicenca $zahtevLicenca
 * @property LicencaTip $tipLicence
 * @property User $user
 * @property Document $documents
 * @property Status $status
 * @property RequestCategory $requestCategory
 */
class SiPrijava extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'si_prijava';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
//    protected $guarded = []; //PRIVREMENO ZBOG KOPIRANJA
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
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

    public static function sendWelcomeEmail($user)
    {
        // Generate a new reset password token
        $token = app('auth.password.broker')->createToken($user);

        // Send email
        Mail::send('emails.welcome', ['user' => $user, 'token' => $token], function ($m) use ($user) {
            $m->from('administrator@ingkomora.rs', 'Inženjerska komora Srbije');
            $m->to($user->email, $user->name)->subject('Prijavite se na sistem');
        });
    }

    public static function existingStatuses()
    {
        $statusi = Status::where('id', '<>', NEAKTIVAN)
            ->whereHas('siPrijave')
            ->orderBy('id')
            ->pluck('naziv', 'id')
            ->toArray();

        return $statusi;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function osoba()
    {
        return $this->belongsTo('App\Models\Osoba', 'osoba_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zvanje()
    {
        return $this->belongsTo('App\Models\Zvanje', 'zvanje_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vrstaPosla()
    {
        return $this->belongsTo('App\Models\VrstaPosla', 'vrsta_posla_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function regOblast()
    {
        return $this->belongsTo('App\Models\RegOblast', 'reg_oblast_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function regPodoblast()
    {
        return $this->belongsTo('App\Models\RegPodoblast', 'reg_pod_oblast_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siVrsta()
    {
        return $this->belongsTo('App\Models\SiVrsta', 'si_vrsta_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function zahtevLicenca()
    {
        return $this->hasOne('App\Models\ZahtevLicenca', 'si_prijava_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *  * TODO rename FK
     */
    public function tipLicence()
    {
        return $this->belongsTo('App\Models\LicencaTip', 'licencatip');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function appKorisnik()
    {
        return $this->belongsTo('App\Models\AppKorisnik', 'app_korisnik_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'app_korisnik_id');
    }

    public static function generatePassword()
    {
        // Generate random string and encrypt it.
        return bcrypt(Str::random(35));
    }

    /**
     * Get all of the post's comments.
     */
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reference()
    {
        return $this->belongsToMany('App\Models\Referenca', 'referenca_si_prijava', 'si_prijava_id', 'referenca_id')
            ->using('App\Models\ReferencaSiPrijava')
            ->withPivot([
                'created_at',
                'updated_at',
            ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Status', 'status_prijave');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requestCategory()
    {
        return $this->belongsTo('App\Models\RequestCategory');
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
    public function getZahtevLicencaStatusAttribute(): string
    {
        if ($this->zahtevLicenca) {
            $result = "{$this->zahtevLicenca->id} | {$this->zahtevLicenca->statusId->naziv}";
        } else {
            $result = '<i class="las la-exclamation-triangle"></i>';
        }
        return $result;
    }

    // todo: basic si prijava document info
    public function getDataDocumentsToArrayAttribute(): array
    {
        $documents = Document::where('documentable_id', $this->id)
            ->where('documentable_type', 'App\Models\SiPrijava')
            ->get(['id', 'document_category_id', 'register_number', 'register_date', 'status_id']);
        $document_data = [];
        foreach ($documents as $key => $document) {
            $document_data[$key] = $document->category_type_name_status_registry_number_registry_date;
        }
        dd($document_data);
        return $document_data;
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
