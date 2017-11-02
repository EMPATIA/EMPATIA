<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomePageConfiguration extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'home_page_configuration_key',
        'home_page_type_id',
        'site_id',
        'value',
        'group_name',
        'group_key'
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
     * A Home Page Configuration belongs to one Site
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site(){
        return $this->belongsTo('App\Site');
    }

    /**
     * A HomePageConfiguration has many Home Page Configuration Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function homePageConfigurationTranslation(){
        return $this->hasMany('App\HomePageConfigurationTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\HomePageConfigurationTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            if (is_null($this->value)){
                $this->setAttribute('value',$translation[0]->value);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        $translations = $this->hasMany('App\HomePageConfigurationTranslation')->get()->keyBy('language_code');
        $this->setAttribute('translations',$translations);
        return $translations;
    }

    /**
     * A Home Page Configuration belongs to one Home Page Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function homePageType(){
        return $this->belongsTo('App\HomePageType');
    }
}
