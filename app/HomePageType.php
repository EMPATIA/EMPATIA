<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomePageType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'home_page_type_key',
        'entity_id',
        'name',
        'code',
        'parent_id',
        'type_code'
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
     * A Home Page Type belongs to one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }

    /**
     * A Home Page Type can have many Home Page Configurations.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function homePageConfigurations(){
        return $this->hasMany('App\HomePageConfiguration');
    }

    public function homePageTypeSons(){
        return $this->hasMany('App\HomePageType', 'parent_id');
    }
}
