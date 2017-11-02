<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Configuration
 * @package App
 */
class Configuration extends Model
{
    use SoftDeletes;    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'configuration_type_id', 'created_by'];

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
     * Defines a many-to-many relationship between CB and Configuration.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cbs()
    {
        return $this->belongsToMany('App\Cb', 'cb_configurations')->withPivot('value', 'created_by')
            ->withTimestamps();
    }    

    /**
     * The configuration that belong to the configuration type.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function configurationType()
    {
        return $this->belongsTo('App\ConfigurationType');
    }        
     
    /**
     * Each configuration type has many configurations.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function configurationOptions() {
        return $this->hasMany('App\ConfigurationOption');
    }

    /**
     * A Configuration has many configuration Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function configurationTranslations(){
        return $this->hasMany('App\ConfigurationTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\ConfigurationTranslation')->where('language_code', '=', $language)->get();
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
        $translations = $this->hasMany('App\ConfigurationTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }

    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->hasMany('App\ConfigurationTranslation')->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->get();
        $this->setAttribute('title',$translation[0]->title);
        $this->setAttribute('description',$translation[0]->description);
    }
}
