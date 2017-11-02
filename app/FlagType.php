<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlagType extends Model
{
    use SoftDeletes;

    protected $fillable = ['code'];

    public function translations() {
        return $this->hasMany('App\FlagTypeTranslation');
    }
    public function currentLanguageTranslation() {
        return $this->translations();
    }
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\FlagTypeTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('title',$translation[0]->title);
            $this->setAttribute('description',$translation[0]->description);
            return true;
        } else {
            return false;
        }
    }
}
