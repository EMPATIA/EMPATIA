<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ConfigurationOption
 * @package App
 */
class ConfigurationOption extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'configuration_id'];

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
     * Each Configuration Type has many Configurations
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function configuration() {
        return $this->belongsTo('App\Configuration');
    }

    /**
     * A Configuration Option has many configuration Option Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function configurationOptionTranslations(){
        return $this->hasMany('App\ConfigurationOptionTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\ConfigurationOptionTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('title',$translation[0]->title);
            $this->setAttribute('description',$translation[0]->description);
            $this->setAttribute('value',$translation[0]->value);
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
        $translations = $this->hasMany('App\ConfigurationOptionTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }

    /**
     * Return the model with dates converted to entity timezone
     *
     * @param $request
     * @return $this
     */
    public function timezone($request) {
        $timezone = empty($request->header('timezone')) ? 'utc' : $request->header('timezone');

        $this->created_at =  is_null($this->created_at) ? null : $this->created_at->timezone($timezone);
        $this->updated_at =  is_null($this->updated_at) ? null : $this->updated_at->timezone($timezone);
        $this->deleted_at =  is_null($this->deleted_at) ? null : $this->deleted_at->timezone($timezone);

        return $this;
    }
}
