<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParameterTemplate extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parameter_template_key', 'parameter_type_id', 'parameter', 'description', 'code', 'mandatory', 'value', 'currency', 'position', 'use_filter', 'visible_in_list', 'visible'];

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
     * Each Parameter belongs to one ParameterType.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type() {
        return $this->belongsTo('App\ParameterType', 'parameter_type_id');
    }

    /**
     * Each Parameter may have many ParameterOptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function templateOptions() {
        return $this->hasMany('App\ParameterTemplateOption');
    }
}
