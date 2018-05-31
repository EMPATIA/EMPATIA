<?php

namespace App;

use Illuminate\Contracts\Console\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Topic
 * @package App
 */
class Topic extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cb_id',
        'id',
        'version',
        'topic_key',
        'created_by',
        'created_on_behalf',
        'title',
        'contents',
        'blocked',
        'q_key',
        'topic_number',
        'start_date',
        'end_date',
        'summary',
        'description',
        'language_code',
        'active',
        'moderated',
        'moderated_by',
        'tag',
        'parent_topic_id'
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
     * Each Topic has many Posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts() {
        return $this->hasMany('App\Post');
    }


    /**
     * Each Topic has many Co-operators.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cooperators() {
        return $this->hasMany('App\Cooperator');
    }

    /**
     * Each Topic belongs to a CB.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cb() {
        return $this->belongsTo('App\Cb');
    }

    /**
     * Each Topic has many likes through Posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function likes() {
        return $this->hasManyThrough('App\PostLike', 'App\Post');
    }

    /**
     * Each Topic has many abuses through Posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function abuses() {
        return $this->hasManyThrough('App\PostAbuse', 'App\Post');
    }

    /**
     * Each Topic has one last Post.
     *
     * @return mixed
     */
    public function lastPost() {
        return $this->hasOne('App\Post')->orderBy('created_at', 'desc');
    }

    /**
     * Each Topic has one first Post.
     *
     * @return mixed
     */
    public function firstPost() {
        return $this->hasOne('App\Post')->orderBy('created_at', 'asc')->where('enabled', '1');
    }

    /**
     * This defines a many-to-many relationship between Topics and Parameters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function parameters() {
        return $this->belongsToMany('App\Parameter', 'topic_parameters')
            ->withPivot('value', 'topic_version_id')
            ->withTimestamps();
    }

    /**
     * Each Topic has many Statuses.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function status() {
        return $this->hasMany('App\Status');
    }
    public function activeStatus() {
        return $this->hasOne('App\Status')->where("active",1);
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
     * Each Topic has many reviews.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicReviews() {
        return $this->hasMany('App\TopicReview');
    }

    /**
     * Each Topic has many TopicFollowers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followers() {
        return $this->hasMany('App\TopicFollower');
    }

    public function originAllyRequest() {
        return $this->hasMany('App\TopicAlliance','origin_topic_id');
    }

    public function destinyAllyRequest() {
        return $this->hasMany('App\TopicAlliance','destiny_topic_id');
    }

    /**
     * Each Topic has many Topic Accesses.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicAccesses() {
        return $this->hasMany('App\TopicAccess');
    }

    /**
     * Each Topic has many News.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function news() {
        return $this->hasMany('App\TopicNews');
    }
    /**
     * Each Topic has many Topic Cb
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicCbs(){
        return $this->hasMany('App\TopicCb');
    }

    /**
     * Each Topic has many Topic Cb
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parentTopic(){
        return $this->hasOne('App\Topic',"id","parent_topic_id");
    }

    public function childTopics(){
        return $this->hasMany('App\Topic','parent_topic_id','id');
    }

    /**
     * Each Topic has many Technical Analysis.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function technicalAnalysis(){
        return $this->hasMany('App\TechnicalAnalysis');
    }

    public function moderation_date()
    {
        return $this->status()->whereActive(1)
            ->whereHas(
                'statusType', function ($query) {
                $query->where('code', '=', 'moderated');
            });
    }

    /**
     * Each Topic has many topic versions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicVersions(){
        return $this->hasMany('App\TopicVersion');
    }

    public function flags(){
        return $this->belongsToMany('App\Flag', 'flag_topic')->withTimestamps()->withPivot('active', 'created_by','id');
    }
}