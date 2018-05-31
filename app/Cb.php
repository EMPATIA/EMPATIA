<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Cb
 * @package App
 */
class Cb extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cb_key',
        'title',
        'contents',
        'created_by',
        'blocked',
        'status_id',
        'layout_code',
        'parent_cb_id',
        'start_date',
        'end_date',
        'tag',
        'template',
        'page_key',
	    'start_topic',
        'end_topic',
        'start_topic_edit',
        'end_topic_edit',
        'start_submit_proposal',
        'end_submit_proposal',
        'start_technical_analysis',
        'end_technical_analysis',
        'start_complaint',
        'end_complaint',
        'start_show_results',
        'end_show_results',
        'start_vote',
        'end_vote',
        'filters',
        '_statistics',
        '_vote_statistics',
        '_cached_data'
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
    protected $hidden = ['deleted_at'];

    /**
     * Each CB has many topics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topics() {
        return $this->hasMany('App\Topic');
    }

    /**
     * Each CB has many Moderators.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function moderators() {
        return $this->hasMany('App\CbModerator');
    }

    /**
     * This defines a many-to-many relationship between configuration and CBs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function configurations(){
        return $this->belongsToMany('App\Configuration', 'cb_configurations')
            ->withPivot('value', 'created_by')
            ->withTimestamps();
    }

    /**
     * Each Cb has many Posts through each Topic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function posts() {
        return $this->hasManyThrough('App\Post', 'App\Topic');
    }

    /**
     * Each Cb can have many CbVotes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes() {
        return $this->hasMany('App\CbVote');
    }

    /**
     * This defines a many-to-many relationship between CBs and Parameters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameters() {
        return $this->hasMany('App\Parameter');
    }

    /**
     * Each Cb has many ParameterOptions through each Parameter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function options() {
        return $this->hasManyThrough('App\ParameterOption', 'App\Parameter');
    }

    public function padPermissions(){
        return $this->hasMany('App\PadPermission');
    }

    public function cb_ConfigurationsPermission(){
        return $this->belongsToMany('App\ConfigurationPermission', 'cb_configuration_permission', 'cb_id', 'config_permission_id')
            ->withPivot('value', 'created_by')
            ->withTimestamps();
    }

    public function cbQuestionnaire(){
        return $this->hasMany('App\CbQuestionnaries');
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

    /**
     * Each Cb has many Topic Accesses through each Topic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function topicAccesses() {
        return $this->hasManyThrough('App\TopicAccess', 'App\Topic');
    }


    /**
     * Each CB has many News.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function news() {
        return $this->hasMany('App\CbNews');
    }


    /**
     * Each Cb has many Topic Cb
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicsCb(){
        return $this->hasMany('App\TopicCb');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function technicalAnalysisQuestions(){
        return $this->hasMany('App\TechnicalAnalysisQuestion');
    }
  /**
     * Each Cb has many CbTemplate
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
    public function cbTemplate() {
        return $this->hasMany('App\CbTemplate');
    }

    /**
     * Each Cb has many Topic Cb
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function flags(){
        return $this->belongsToMany('App\Flag', 'cb_flag')->withTimestamps()->withPivot('active', 'created_by','id');
    }

    /**
       * Each Cb has many CbTranslation
       * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
      public function cbTranslations() {
          return $this->hasMany('App\CbTranslation');
      }

      /**
       * Each Cb has many Translation
       * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translationCode() {
        return $this->hasMany('App\TranslationCode');
    }

    /**
     * Each Cb has many CbMenuTranslation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cbMenuTranslations() {
        return $this->hasMany('App\CbMenuTranslation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operationSchedules() {
        return $this->hasMany('App\CbOperationSchedule');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs() {
        return $this->hasMany('App\Cb', 'parent_cb_id','id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent() {
        return $this->hasOne('App\Cb', 'parent_cb_id', 'id');
    }


}
