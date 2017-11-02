<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionParameter extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'section_parameter_key',
        'section_id',
        'section_type_parameter_id',
        'code',
        'value'
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
     * Each Section Parameter belongs to a Section
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section() {
        return $this->belongsTo('App\Section');
    }

    /**
     * Each Section Parameter belongs to a Section Type Parameter
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sectionTypeParameter() {
        return $this->belongsTo('App\SectionTypeParameter');
    }

    /**
     * Each Section has Many Section Parameter Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sectionParameterTranslations() {
        return $this->hasMany('App\SectionParameterTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null) {
        $translation = $this->hasMany('App\SectionParameterTranslation')->where('language_code', '=', $language)->get();
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
        $translations = $this->hasMany('App\SectionParameterTranslation')->get()->keyBy('language_code');
        $this->setAttribute('translations',$translations);
        return $translations;
    }

    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->sectionParameterTranslations->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->get();
        $this->setAttribute('value',$translation[0]->value ?? null);
    }
}
