<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParameterOption extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parameter_id',
        'label',
        'code'
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

    /**
     * Each ParameterOption belongs to a Parameter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parameter() {
        return $this->belongsTo('App\Parameter');
    }

    /**
     * A Parameter Option has many Parameter Option Translation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameterOptionTranslations(){
        return $this->hasMany('App\ParameterOptionTranslation');
    }

    public function parameterOptionFields(){
        return $this->hasMany('App\ParameterOptionField', 'parameter_option_id', 'id');
    }

    public function padPermissions(){
        return $this->belongsToMany('App\PadPermission', 'parameter_option_permissions')
            ->withPivot('pad_permission_id')
            ->withTimestamps();
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\ParameterOptionTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('label',$translation[0]->label);
            return true;
        } else {
            return false;
        }
    }

    public function translationByArray($values)
    {
        $this->setAttribute('label',$values['label']?? '');
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        $translations = $this->hasMany('App\ParameterOptionTranslation')->get()->keyBy('language_code');
        $this->setAttribute('translations',$translations);
        return $translations;
    }


    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->hasMany('App\ParameterOptionTranslation')->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->first();
        $this->setAttribute('label',$translation->label ?? null);
    }
}
