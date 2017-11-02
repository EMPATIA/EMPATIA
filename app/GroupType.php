<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupType extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'group_type_key',
        'name',
        'code'
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
     * GroupType can have many EntityGroups
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entityGroups()
    {
        return $this->hasMany('App\EntityGroup');
    }
}
