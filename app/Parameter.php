<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parameter extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parameter_type_id',
        'cb_id',
        'code',
        'parameter_code',
        'mandatory',
        'value',
        'currency',
        'position',
        'use_filter',
        'visible_in_list',
        'visible',
        'private'
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
    protected $hidden = ['deleted_at'];

    public function parameterFields(){
        return $this->hasMany('App\ParameterField');
    }

    /**
     * Each Parameter belongs to one ParameterType.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type() {
        return $this->belongsTo('App\ParameterType', 'parameter_type_id');
    }

    /**
     * Each Parameter may have many ParameterOptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options() {
        return $this->hasMany('App\ParameterOption');
    }

    /**
     * This defines a many-to-many relationship between CBs and Parameters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cbs() {
        return $this->belongsTo('App\Cb');
    }

    /**
     * This defines a many-to-many relationship between Topics and Parameters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function topics() {
        return $this->belongsToMany('App\Topic', 'topic_parameters')
            ->withPivot('value', 'topic_version_id')
            ->withTimestamps();
    }

    /**
     * A Parameter has many Parameter Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameterTranslations(){
        return $this->hasMany('App\ParameterTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\ParameterTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('parameter',$translation[0]->parameter);
            $this->setAttribute('description',$translation[0]->description);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $values
     * @return bool
     * @internal param null $language
     */
    public function translationByArray($values)
    {
        $this->setAttribute('parameter',$values['parameter']?? '');
        $this->setAttribute('description',$values['description']?? '');
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        $translations = $this->hasMany('App\ParameterTranslation')->get()->keyBy('language_code');
        $this->setAttribute('translations',$translations);
        return $translations;
    }

    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->hasMany('App\ParameterTranslation')->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->first();
        $this->setAttribute('parameter',$translation->parameter ?? null);
        $this->setAttribute('description',$translation->description ?? null);
    }
}
