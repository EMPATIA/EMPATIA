<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParameterTemplateOption extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parameter_template_id', 'label'];

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
     * Each ParameterOption belongs to a Parameter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parameterTemplate() {
        return $this->belongsTo('App\ParameterTemplate');
    }
}
