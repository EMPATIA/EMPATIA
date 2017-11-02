<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'section_type_key',
        'code',
        'translatable'
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
     * Each Section Type has Many Sections
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections() {
        return $this->hasMany('App\Section');
    }

    /**
     * Each Section Type has Many Section Type Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sectionTypeTranslations() {
        return $this->hasMany('App\SectionTypeTranslation');
    }

    /**
     * This defines a many-to-many relationship between Section Types and Section Type Parameters.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sectionTypeParameters(){
        return $this->belongsToMany('App\SectionTypeParameter');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\SectionTypeTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
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
        $translations = $this->hasMany('App\SectionTypeTranslation')->get()->keyBy('language_code');
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
