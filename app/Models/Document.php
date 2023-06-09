<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $document_category_id
 * @property integer $document_type_id
 * @property integer $registry_id
 * @property integer $status_id
 * @property integer $user_id
 * @property string $registry_number
 * @property string $registry_date
 * @property string $path
 * @property string $location
 * @property string $barcode
 * @property string $metadata
 * @property string $note
 * @property string $documentable_type
 * @property integer $documentable_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $sent_at
 * @property string $valid_from
 * @property DocumentCategory $documentCategory
 * @property DocumentType $documentType
 * @property Registry $registry
 * @property Status $status
 * @property User $user
 * @property Document $documentable
 *
 */
class Document extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'documents';
    // protected $primaryKey = 'id';
//    public $timestamps = FALSE; //PRIVEREMENO ZBOG KOPIRANJA
    protected $guarded = ['id'];
//    protected $guarded = [];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
//    protected $appends = ['category_name_registry_number', 'category_name_status_registry_number', 'category_type_name_status_registry_number'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function metadataFormating()
    {
        $return = '';
        if (!is_null($this->metadata)) {
            foreach (json_decode($this->metadata, TRUE) as $key => $value) {
                $return .= ucfirst($key) . ': ' . ucfirst($value) . '<br>';
            };
        }
        return $return;
    }

//    public function relatedModel()
//    {
////        return "<a href='" . $this->documentable_type . "'>$this->documentable_type</a>";
//        echo $this->documentable_type;
//    }


    /**
     * Model-function for backpack crud morph relation.
     *
     * @param string $value
     * @return string
     */
    public function getOsobaImePrezimeJmbg()
    {
        if ($this->documentable_type == "App\Models\ZahtevLicenca") {
            return $this->documentable->osobaId->ime_prezime_jmbg;
        } else if ($this->documentable_type == "App\Models\SiPrijava") {
            return $this->documentable->osoba->ime_prezime_jmbg;
        } else {
            return $this->documentable->osoba->ime_prezime_jmbg;
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
    public function documentCategory()
    {
        return $this->belongsTo('App\Models\DocumentCategory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function registry()
    {
        return $this->belongsTo('App\Models\Registry');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Get the parent commentable model (post or video).
     */
    public function documentable()
    {
        return $this->morphTo();
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
     * Get the user's Document category name and registry number.
     *
     * @param string $value
     * @return string
     */
    public function getCategoryNameRegistryNumberAttribute()
    {
        return "{$this->documentCategory->name} ($this->registry_number)";
    }

    /**
     * Get the user's Document category name, status and registry number.
     *
     * @param string $value
     * @return string
     */
    public function getCategoryNameStatusRegistryNumberAttribute()
    {
        return "{$this->documentCategory->name} ({$this->status->naziv} | $this->registry_number)";
    }

    /**
     * Get the user's Document category type name, status and registry number.
     *
     * @param string $value
     * @return string
     */
    public function getCategoryTypeNameStatusRegistryNumberAttribute()
    {
        return "{$this->documentCategory->documentCategoryType->name} ({$this->status->naziv} | $this->registry_number)";
    }

    public function getCategoryTypeNameStatusRegistryDateAttribute(): string
    {
        if (!empty($this->registry_date))
            $registry_date = Carbon::parse($this->registry_date)->format('d.m.Y');
        else
            $registry_date = '';

        return "{$this->documentCategory->documentCategoryType->name} ({$this->status->naziv} | $registry_date)";
    }

    public function getCategoryTypeNameStatusRegistryNumberRegistryDateAttribute(): string
    {
        if (!empty($this->registry_date))
            $registry_date = Carbon::parse($this->registry_date)->format('d.m.Y');
        else
            $registry_date = '';

        return "{$this->documentCategory->documentCategoryType->name} ({$this->status->naziv} | $this->registry_number | $registry_date)";
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
