<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntityGroup extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'entity_id',
        'entity_group_key',
        'parent_group_id',
        'group_type_id',
        'designation',
        'name',
        'position'
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



    /**
     * An EntityGroup has one GroupType
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupType(){
        return $this->belongsTo('App\GroupType');
    }

    /**
     * An EntityGroup belongs to one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }

    /**
     * An EntityGroup belongs to one EntityGroup
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entityGroup(){
        return $this->belongsTo('App\EntityGroup','parent_group_id');
    }

    /**
     * An EntityGroup belongs to many Users     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(){
        return $this->belongsToMany('App\OrchUser','entity_group_user','entity_group_id','user_id')
            ->withTimestamps();
    }

    /**
     * An EntityGroup can have many Entity Permissions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityPermissions(){
        return $this->hasMany('App\EntityPermission');
    }
}
