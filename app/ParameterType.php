<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParameterType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'code', 'options'];

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
     * Each ParameterType has many Parameters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameters() {
        return $this->hasMany('App\Parameter');
    }

    public function paramAddFields() {
        return $this->hasMany('App\ParamAddField', 'parameter_type_id', 'id');
    }

    /**
     * Each Parameter User Type has many Parameter User Options.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameterOptions() {
        return $this->hasMany('App\ParameterOption');
    }
}
