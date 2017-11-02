<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Post
 * @package App
 */
class Post extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['post_key', 'id','version','topic_id', 'created_by', 'contents', 'status_id', 'parent_id', 'enabled', 'blocked', 'active', 'post_comment_type_id'];

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
    protected $hidden = [];

    /**
     * Each Post belongs to a Topic.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic() {
        return $this->belongsTo('App\Topic');
    }

    /**
     * Each Post has many Post Abuses.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function abuses() {
        return $this->hasMany('App\PostAbuse');
    }
    
    /**
     * Each Post has many Post Likes.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes() {
        return $this->hasMany('App\PostLike');
    }

    /**
     * Each Post may have many Files.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files() {
        return $this->hasMany('App\PostFile');
    }

    /**
     * Each Post may have many Annotations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function annotations() {
        return $this->hasMany('App\Annotation');
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
     * Each Post belongs to a Post Comment Type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postCommentType() {
        return $this->belongsTo('App\PostCommentType');
    }

    /**
     * Each Cb has many Topic Cb
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function flags(){
        return $this->belongsToMany('App\Flag', 'flag_post')->withTimestamps()->withPivot('active', 'created_by','id');
    }
}
