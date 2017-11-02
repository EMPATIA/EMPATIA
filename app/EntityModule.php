<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntityModule extends Model
{
    protected $fillable = [
        'entity_module_key',
        'entity_id',
        'module_id'
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
    protected $hidden = ['id', 'deleted_at'];

    /**
     * An EntityModule can have many EntityModuleTypes
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityModuleTypes(){
        return $this->hasMany('App\EntityModuleType');
    }

    /**
     * An Entity Module belongs to one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }

    /**
     * An Entity Module belongs to one Module
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(){
        return $this->belongsTo('App\Module');
    }
}
