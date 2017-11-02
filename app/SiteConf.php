<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteConf extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'site_conf_key',
        'site_conf_group_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['start_date', 'end_date', 'deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];

    public function scopeSiteConfKey($query, $key)
    {
        $query->where('site_conf_key', '=', $key);
    }

    /**
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\SiteConfTranslation')->where('lang_code', '=', $language)->get();
        if (sizeof($translation) > 0) {
            if (is_null($this->value)) {
                $this->setAttribute('name', $translation[0]->name);
                $this->setAttribute('description', $translation[0]->description);
            }
            return true;
        } else {
            return false;
        }
    }

    public function newTranslation($language = null, $languageDefault = null)
    {
        $translation = $this->hasMany('App\SiteConfTranslation')->orderByRaw("FIELD(lang_code,'".$languageDefault."','".$language."')DESC")->get();
        $this->setAttribute('name',$translation[0]->name ?? null);
        $this->setAttribute('description',$translation[0]->description ?? null);
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        $translations = $this->hasMany('App\SiteConfTranslation')->get()->keyBy('lang_code');
        $this->setAttribute('translations', $translations);
        return $translations;
    }

    /**
     * A HomePageConfiguration has many Home Page Configuration Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteConfTranslation()
    {
        return $this->hasMany('App\SiteConfTranslation');
    }

    /**
     * @return mixed
     */
    public function siteSiteConfs()
    {
        $siteSiteConfs = $this->hasMany('App\SiteSiteConfs')->get();
        $this->setAttribute('siteSiteConfs', $siteSiteConfs);
    }
    /**
     * A HomePageConfiguration has many Home Page Configuration Translations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteSiteConf()
    {
        return $this->hasMany('App\SiteSiteConfs');
    }

    public function siteConfValues($siteId=null)
    {
        $siteConfValues =  $this->hasMany('App\SiteConfValue')->whereSiteId($siteId)->get();
        $this->setAttribute('siteConfValues', $siteConfValues);
        return $siteConfValues;
    }
}
