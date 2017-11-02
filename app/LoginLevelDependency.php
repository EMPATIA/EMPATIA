<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginLevelDependency extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'login_level_id',
        'dependency_login_level_id',
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
     * @var array
     */
    protected $hidden = ['deleted_at'];


    /**
     * Each Login Level Dependency belongs to one Login Level - login level
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loginLevel(){
        return $this->belongsTo('App\LoginLevel','login_level_id','id');
    }

    /**
     * Each Login Level Dependency belongs to one Login Level - dependency
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loginLevelDependency(){
        return $this->belongsTo('App\LoginLevel','dependency_login_level_id','id');
    }

}
