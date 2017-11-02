<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['color','icon', 'pin', 'max_value', 'min_value'];

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

    public function paramAddFields()
    {
        $this->hasMany('App\ParamAddField', 'field_type_id', 'id');
    }

    public function parameterOptionFields()
    {
        $this->hasMany('App\ParameterOptionField', 'field_type_id', 'id');
    }

}
