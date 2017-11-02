<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class BEMenuElement extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'code',
        'module_code',
        'module_type_code',
        'permission',
        'controller',
        'method'
    ];

    protected $table = "be_menu_elements";

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
        return $this->belongsToMany('App\BEMenuElementParameter','be_menu_element_parameter_relation','be_menu_parameter_id','be_menu_element_id')
            ->withPivot('position','code')
            ->withTimestamps();
    }
    
    /**
     * Each BE Menu Element has Many BE Menu Element Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations() {
        return $this->hasMany('App\BEMenuElementTranslation','be_menu_element_id');
    }

    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->translations()->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."') DESC")->first();
        $this->setAttribute('name',$translation->name ?? "");
        $this->setAttribute('description',$translation->description ?? "");
    }
}