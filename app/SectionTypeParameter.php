<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionTypeParameter extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'section_type_parameter_key',
        'code',
        'type_code'
    ];

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

    /**
     * Each Section Type Parameter has Many Section Parameters
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sectionParameters() {
        return $this->hasMany('App\SectionParameter');
    }

    /**
     * Each Section Type has Many Section Type Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sectionTypeParameterTranslations() {
        return $this->hasMany('App\SectionTypeParameterTranslation');
    }

    /**
     * This defines a many-to-many relationship between Section Types and Section Type Parameters.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sectionTypes(){
        return $this->belongsToMany('App\SectionType');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\SectionTypeParameterTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('name',$translation[0]->name);
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
        $translations = $this->hasMany('App\SectionTypeParameterTranslation')->get()->keyBy('language_code');
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
