<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrijavaPromenaPodataka extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tprijavapromenapodataka';

    /**
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function licenca()
    {
        return $this->belongsTo('App\Models\Licenca', 'brlic');
    }

    public function opstina()
    {
        return $this->belongsTo('App\Models\Opstina', 'topstina_id');
    }
}
