<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'cm_key',
        'layout_id',
        'entity_id',
        'name',
        'description',
        'link',
        'partial_link',
        'active',
        'no_reply_email',
        'start_date',
        'end_date'
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
    protected $hidden = ['id','deleted_at'];

    /**
     * Site has one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity');
    }

    /**
     * Site Method has many Discussions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function discussions(){

        return $this->hasMany('App\Discussion');
    }

    /**
     * Site Method has many Access Menus
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessMenus(){

        return $this->hasMany('App\AccessMenu');
    }

    /**
     * Site has one Layout
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function layout(){
        return $this->belongsTo('App\Layout');
    }

    /**
     * A Site can have many Home Page Configurations.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function homePageConfigurations(){
        return $this->hasMany('App\HomePageConfiguration');
    }


    /**
     * A Site can have many HNewsletter Subscriptions.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function newsletterSubscriptions(){
        return $this->hasMany('App\NewsletterSubscription');
    }

    /**
     * A Site has many Use Terms
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function useTerms(){
        return $this->hasMany('App\UseTerm');
    }

    /**
     * A Site has many Configurations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function confs() {
        return $this->hasMany("App\SiteConf");
    }

    /**
     * A Site has many LevelParameters
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function levelParameters(){
        return $this->hasMany('App\LevelParameter');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialNetworks(){
        return $this->hasMany('App\OrchSocialNetwork');
    }

    /**site
     * A Site can have many URL's
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function additionalUrls(){
        return $this->hasMany('App\SiteAdditionalUrl');
    }

    /**
     * A Site has many Site Ethics
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteEthics(){
        return $this->hasMany('App\SiteEthic');
    }

    public function configurationsValues() {
        return $this->hasMany("App\SiteConfValue");
    }

          /**
       * Each Site has many Translation
       * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translationCode() {
        return $this->hasMany('App\TranslationCode');
    }
}
