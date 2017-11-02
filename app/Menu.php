<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Menu
 * @package App
 */
class Menu extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['menu_key', 'parent_id','access_id', 'type_id', 'position', 'type', 'value'];

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
     * Each Menu has one Page
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function page() {
        return $this->hasOne('App\Page');
    }

    /**
     * Each Menu has many Translations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menuTranslations() {
        return $this->hasMany('App\MenuTranslation');
    }

    public function translation($language = null)
    {
        $translation = $this->hasMany('App\MenuTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('title',$translation[0]->title);
            $this->setAttribute('link',$translation[0]->link);
            return true;
        } else {
            return false;
        }
    }

    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->hasMany('App\MenuTranslation')->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->first();
        $this->setAttribute('title',$translation->title ?? null);
        $this->setAttribute('link',$translation->link ?? null);
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        $translations = $this->hasMany('App\MenuTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }

    /**
     * Each Menu has many Menu Types
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menuTypes() {
        return $this->belongsTo('App\MenuType', 'type_id');
    }
}
