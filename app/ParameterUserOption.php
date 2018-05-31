<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParameterUserOption extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parameter_user_option_key', 'parameter_user_type_id'];

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
     * A Parameter User Option belongs to a Parameter User Type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parameterUserType(){
        return $this->belongsTo('App\ParameterUserType');
    }

    /**
     * Each Parameter User Option has many Parameter User Option Translations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameterUserOptionTranslations() {
        return $this->hasMany('App\ParameterUserOptionTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\ParameterUserOptionTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('name',$translation[0]->name);
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
        $translations = $this->hasMany('App\ParameterUserOptionTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }

    public function newTranslation($language = null, $languageDefault = null) {
        $translation = $this->hasMany('App\ParameterUserOptionTranslation')->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->first();
        $this->setAttribute('name',$translation->name ?? null);
    }

    public function userParameters() {
        return $this->hasMany("App\UserParameter","value","parameter_user_option_key");
    }
}
