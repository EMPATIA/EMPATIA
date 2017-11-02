<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentTypeType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content_type_id', 'content_type_type_key', 'code', 'entity_key', 'color', 'text_color', 'file'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Each ContentTypeType may have many Content
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents() {
        return $this->hasMany('App\Content');
    }

    /**
     * Each ContentTypeType has one ContentType
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentType()
    {
        return $this->belongsTo('App\ContentType');
    }

    /**
     * A Parameter Option has many Parameter Option Translation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contentTypeTypeTranslations(){
        return $this->hasMany('App\ContentTypeTypeTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\ContentTypeTypeTranslation')->where('language_code', '=', $language)->get();
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
        $translations = $this->hasMany('App\ContentTypeTypeTranslation')->get()->keyBy('language_code');
        $this->setAttribute('translations',$translations);
        return $translations;
    }

    /**
     * @param null $language
     * @return bool
     */
    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->hasMany('App\ContentTypeTypeTranslation')->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->first();
        $this->setAttribute('name',$translation->name ?? null);
    }

}
