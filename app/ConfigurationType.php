<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ConfigurationType
 * @package App
 */
class ConfigurationType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['created_by', 'code'];

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
     * Each configuration type has many configurations
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function configurations() {
        return $this->hasMany('App\Configuration');
    }

    /**
     * A Configuration Type has many configuration Type Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function configurationTypeTranslations(){
        return $this->hasMany('App\ConfigurationTypeTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\ConfigurationTypeTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('title',$translation[0]->title);
            $this->setAttribute('description',$translation[0]->description);
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
        $translations = $this->hasMany('App\ConfigurationTypeTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
