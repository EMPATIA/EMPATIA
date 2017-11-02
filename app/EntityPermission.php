<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntityPermission extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'entity_permission_key',
        'entity_group_id',
        'entity_id',
        'user_id',
        'module_id',
        'module_type_id',
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
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'deleted_at',
        'entity_group_id',
        'entity_id',
        'user_id',
        'module_id',
        'module_type_id',];


    /**
     * An EntityPermission belongs to one Module Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function moduleType(){
        return $this->belongsTo('App\ModuleType');
    }

    /**
     * An EntityPermission belongs to one Module
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(){
        return $this->belongsTo('App\Module');
    }

    /**
     * An EntityPermission belongs to one Entity Group
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entityGroup(){
        return $this->belongsTo('App\EntityGroup');
    }

    /**
     * An EntityPermission belongs to one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }

    /**
     * An EntityPermission belongs to one User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\OrchUser','user_id');
    }


}
