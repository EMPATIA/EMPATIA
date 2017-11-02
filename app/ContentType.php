<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['display_linkable','linkable','code'];

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
    protected $hidden = ['deleted_at','pivot'];
    
    /**
     * Each ContentType may have many Content
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents() {
        return $this->hasMany('App\Content');
    }

    /**
     * Each Content Type may have many Contents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function newContents() {
        return $this->hasMany('App\NewContent');
    }
    
    /**
     * Each ContentType has many Translations
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contentTypeTranslations() {
        return $this->hasMany('App\ContentTypeTranslation');
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\ContentTypeTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('title',$translation[0]->title);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Each ContentType may have many ContentTypeType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contentTypeTypes() {
        return $this->hasMany('App\ContentTypeType');
    }
}
