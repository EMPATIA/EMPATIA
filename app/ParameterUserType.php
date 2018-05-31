<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParameterUserType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parameter_user_type_key', 'code','parameter_type_id', 'mandatory','parameter_unique', 'anonymizable','level_parameter_id','vote_in_person','external_validation'];

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
     * A Parameter User Type belongs to an Entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }

    /**
     * A Parameter User Type belongs to a Parameter Type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parameterType(){
        return $this->belongsTo('App\OrchParameterType', 'parameter_type_id');
    }

    /**
     * A Parameter User Type belongs to a Level Parameter.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function levelParameter(){
        return $this->belongsTo('App\LevelParameter');
    }

    /**
     * Each Parameter User Type has many Parameter User Options.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameterUserOptions() {
        return $this->hasMany('App\ParameterUserOption');
    }

    /**
     * Each Parameter User Type has many Parameter User Type Translations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameterUserTypeTranslations() {
        return $this->hasMany('App\ParameterUserTypeTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\ParameterUserTypeTranslation')->where('language_code', '=', $language)->get();
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
        $translations = $this->hasMany('App\ParameterUserTypeTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }

    /**
     * A ParameterUserType has many LoginLevels
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function levels(){
        return $this->hasMany('App\LoginLevelParameter');
    }


    public function newTranslation($language = null, $languageDefault = null) {
        $translation = $this->hasMany('App\ParameterUserTypeTranslation')->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->first();
        $this->setAttribute('name',$translation->name ?? null);
    }

    public function userParameters() {
        return $this->hasMany("App\UserParameter","parameter_user_type_key","parameter_user_type_key");
    }
}
