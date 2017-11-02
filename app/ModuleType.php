<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['module_type_key','code', 'name', 'module_id'];

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
     * Each Module Type belongs to one Module.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module() {
        return $this->belongsTo('App\Module');
    }

    /**
     * Each Module Type may have many Entity Module Types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityModuleTypes() {
        return $this->hasMany('App\EntityModuleType');
    }

    /**
     * An Module Type can have many Entity Permissions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityPermissions(){
        return $this->hasMany('App\EntityPermission');
    }

}
