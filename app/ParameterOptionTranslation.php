<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParameterOptionTranslation extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parameter_option_id', 'language_code', 'label'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];

    /**
     * A Parameter Translation belongs to one Parameter
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parameterOption(){
        return $this->belongsTo('App\ParameterOption');
    }
}
