<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['module_key','name','code', 'token'];

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
    protected $hidden = ['id', 'deleted_at'];

    /**
     * Each Module may has many Module Types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function moduleTypes() {
        return $this->hasMany('App\ModuleType');
    }

    /**
     * Each Module may has many Entity Modules.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityModules() {
        return $this->hasMany('App\EntityModule');
    }

    /**
     * An Module can have many Entity Permissions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityPermissions(){
        return $this->hasMany('App\EntityPermission');
    }

}
