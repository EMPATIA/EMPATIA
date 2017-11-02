<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EmailText
 * @package App
 */
class EmailText extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

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
     * Each EmailText has many EmailTextTranslations.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailTextTranslations() {
        return $this->hasMany('App\EmailTextTranslation');
    }

    public function translation($language = null)
    {
        $translation = $this->hasMany('App\EmailTextTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('subject',$translation[0]->subject);
            $this->setAttribute('body',$translation[0]->body);
            $this->setAttribute('tag',$translation[0]->tag);
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
        $translations = $this->hasMany('App\EmailTextTranslation')->get();
        $this->setAttribute('translations',$translations);
        return $translations;
    }

}
