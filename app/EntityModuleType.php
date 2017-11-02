<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EntityModuleType extends Model
{
    protected $fillable = [
        'entity_module_id',
        'module_type_id'
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
     * An Entity Module Type belongs to one Entity Module
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entityModule(){
        return $this->belongsTo('App\EntityModule');
    }

    /**
     * An Entity Module Type belongs to one Module Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function moduleType(){
        return $this->belongsTo('App\ModuleType');
    }
}
