<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteConfGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'site_conf_group_key',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['start_date','end_date','deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];

    /*
     * SiteConfGroup has many SiteConf
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteConf() {
        return $this->hasMany("App\SiteConf");
    }

    public function scopeSiteConfGroupKey($query,$key) {
        $query->where('site_conf_group_key','=',$key);
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\SiteConfGroupTranslation')->where('lang_code', '=', $language)->get();
        if(sizeof($translation)>0){
            if (is_null($this->value)) {
                $this->setAttribute('name',$translation[0]->name);
                $this->setAttribute('description',$translation[0]->description);
            }
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
        $translations = $this->hasMany('App\SiteConfGroupTranslation')->get()->keyBy('lang_code');
        $this->setAttribute('translations',$translations);
        return $translations;
    }


    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->hasMany('App\SiteConfGroupTranslation')->orderByRaw("FIELD(lang_code,'".$languageDefault."','".$language."')DESC")->get();
        $this->setAttribute('name',$translation[0]->name ?? null);
        $this->setAttribute('description',$translation[0]->description ?? null);
    }

    /**
     * A HomePageConfiguration has many Home Page Configuration Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteConfGroupTranslation(){
        return $this->hasMany('App\SiteConfGroupTranslation');
    }

    /**
 * @return mixed
 */
    public function siteConfs()
    {
        $siteConf = $this->hasMany('App\SiteConf')->get();
        $this->setAttribute('siteConf', $siteConf);
    }


    /**
     * @return mixed
     */
    public function siteConfigurations()
    {
        return $this->hasMany('App\SiteConf');
    }
}
