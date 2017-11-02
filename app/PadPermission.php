<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PadPermission extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_key',
        'user_key',
        'permission_show',
        'permission_create',
        'permission_update',
        'permission_delete',
        'created_by',
        'updated_by'
    ];

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

    function parameterOptions(){
        return $this->belongsToMany('App\ParameterOption', 'parameter_option_permissions')
            ->withPivot('parameter_option_id')
            ->withTimestamps();
    }

    function cbs(){
        return $this->belongsTo('App\Cb');
    }
}
