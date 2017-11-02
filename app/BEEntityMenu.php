<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class BEEntityMenu extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'user_key'
    ];

    protected $table = "be_entity_menus";

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = ['deleted_at'];


    public function entity(){
        return $this->belongsTo('App\Entity');
    }

    public function elements() {
        return $this->hasMany('App\BEEntityMenuElement','be_entity_menu_id');
    }

    public function orderedElements() {
        return $this->elements()->orderBy("position","ASC");
    }

    public function user() {
        return $this->belongsTo('App\User','user_key','user_key');
    }
    public function orchUser() {
        return $this->belongsTo('App\OrchUser','user_key','user_key');
    }
}