<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnotationType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['annotation_type_key', 'code'];

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
     * This defines a many-to-many relationship between Annotation Types and Annotations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function annotations() {
        return $this->belongsToMany('App\Annotation');
    }

    /**
     * An Annotation Type has many Annotation Type Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function annotationTypeTranslations(){
        return $this->hasMany('App\AnnotationTypeTranslation');
    }


    /**
     * @param null $request
     * @return bool
     */
    public function translation($request = null)
    {
        $attributes = [
            'value',
        ];

        $preferredLanguage  = $request->header('LANG-CODE');
        $defaultLanguage    = $request->header('LANG-CODE-DEFAULT');

        $translation = $this->hasMany('App\AnnotationTypeTranslation')->where('language_code', '=', $preferredLanguage)->get();
        if(sizeof($translation)>0){
            foreach ($attributes as $attribute) {
                $this->setAttribute($attribute, $translation[0]->$attribute);
            }
            return true;
        } else {
            $translation = $this->hasMany('App\AnnotationTypeTranslation')->where('language_code', '=', $defaultLanguage)->get();
            if (sizeof($translation) > 0) {
                foreach ($attributes as $attribute) {
                    $this->setAttribute($attribute, $translation[0]->$attribute);
                }
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        $translations = $this->hasMany('App\AnnotationTypeTranslation')->get();
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
