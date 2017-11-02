<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class BEEntityMenuElement extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'menu_key',
        'position',
        'be_menu_element_id',
        'be_entity_menu_id',
        'parent_id'
    ];

    protected $table = "be_entity_menu_elements";

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

    
    public function parameters() {
        return $this->hasMany('App\BEEntityMenuElementParameter','be_entity_menu_element_id');
    }

    /**
     * Each BE Menu Element has Many BE Menu Element Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations() {
        return $this->hasMany('App\BEEntityMenuElementTranslation','be_entity_menu_element_id');
    }

    public function newTranslation($language = null, $languageDefault = null) {
        $translation = $this->translations()->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."') DESC")->first();
        $this->setAttribute('name',$translation->name ?? "");

        return array();
    }

    public function menuElement() {
        return $this->belongsTo('App\BEMenuElement','be_menu_element_id','id');
    }

    public function entityMenu() {
        return $this->belongsTo('App\BEEntityMenu','be_entity_menu_id');
    }

    public function childs() {
        return $this->hasMany('App\BEEntityMenuElement','parent_id');
    }
}