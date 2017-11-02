<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layout extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'layout_key',
        'name',
        'reference'
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
    protected $hidden = ['id','deleted_at'];

    /**
     * A Layout can have many Sites
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sites(){
        return $this->hasMany('App\Site');
    }

    /**
     * A Layout can have many Entities
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function entities(){
        return $this->belongsToMany('App\Entity');
    }
}
