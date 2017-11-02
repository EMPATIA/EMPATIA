<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginLevel extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'login_level_key',
        'entity_id',
        'sms_verification',
        'manual_verification',
        'email_verification',
        'name',
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
     * A Login Level has many Parameter User Types
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameterUserTypes(){
        return $this->hasMany('App\ParameterUserType');
    }

    /**
     * Each Level Parameter belongs to one Entity.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity() {
        return $this->belongsTo('App\Entity');
    }

    /**
     * Each Login Level has many dependencies - Login Level
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loginLevelDependencies(){
        return $this->hasMany('App\LoginLevelDependency','login_level_id');
    }

    /**
     * Each Dependency - Login Level -  has many Login levels
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loginLevels(){
        return $this->hasMany('App\LoginLevelDependency','dependency_login_level_id');
    }


    /**
     * Each Login Level has many User Login Levels
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loginLevelUsers(){
        return $this->hasMany('App\UserLoginLevel','login_level_id');
    }

    /**
     * Each Login Level has many User Login Levels
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameters(){
        return $this->hasMany('App\LoginLevelParameter');
    }


}
