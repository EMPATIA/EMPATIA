<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flag extends Model
{
    use SoftDeletes;

    protected $fillable = ['flag_type_id','position','flag_visible','public_visible','private_flag'];


    public function translations() {
        return $this->hasMany('App\FlagTranslation');
    }

    public function currentLanguageTranslation() {
        return $this->translations()->get();
    }

    public function translation($language = null)
    {
        $translation = $this->hasMany('App\FlagTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('title',$translation[0]->title);
            $this->setAttribute('description',$translation[0]->description);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Each Parameter belongs to one ParameterType.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('App\FlagType', 'flag_type_id');
    }

    /**
     * Each Cb has many Topic Cb
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(){
        return $this->belongsToMany('App\Post', 'flag_post')->withTimestamps()->withPivot('active', 'created_by','id');
    }
}
