<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteEthic extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_ethic_key',
        'version',
        'active',
        'site_ethic_type_id'
    ];

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
    protected $hidden = ['id','deleted_at'];

    /**
     * Each Site Ethic belongs to one Site
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site(){
        return $this->belongsTo('App\Site');
    }

    /**
     * Each Site Ethic belongs to one Site Ethic Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siteEthicType(){
        return $this->belongsTo('App\SiteEthicType');
    }


    /**
     * Each Site Ethic has many Site Ethic Translations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteEthicTranslations() {
        return $this->hasMany('App\SiteEthicTranslation');
    }


    /** Get Site Ethic Translation by lang code
     *
     * @param null $language
     * @return bool
     */
    public function translation($language = null)
    {
        $translation = $this->hasMany('App\SiteEthicTranslation')->where('language_code', '=', $language)->get();
        if(sizeof($translation)>0){
            $this->setAttribute('content',$translation[0]->content);
            return true;
        } else {
            return false;
        }
    }

    /** Get all Site Ethic translations
     * @return mixed
     */
    public function translations()
    {
        $translations = $this->hasMany('App\SiteEthicTranslation')->get()->keyBy('language_code');
        $this->setAttribute('translations',$translations);
        return $translations;
    }
}
