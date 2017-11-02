<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParamAddField extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['field_type_id', 'parameter_type_id', 'code', 'value'];

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

    public function parameterTypes(){
        return $this->belongsTo('App\ParameterType');
    }

    public function paramFieldTypes()
    {
        return $this->belongsTo('App\FieldType');
    }

    /**
     * A Parameter Option has many Parameter Option Translation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paramAddFieldTranslations(){
        return $this->hasMany('App\ParamAddFieldTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\ParamAddFieldTranslation')->where('language_code', '=', $language)->get();
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
        $translations = $this->hasMany('App\ParamAddFieldTranslation')->get()->keyBy('language_code');
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
