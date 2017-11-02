<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicReview extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'topic_review_key',
        'topic_id',
        'description',
        'subject',
        'created_by'
    ];

    /**
     * Each TopicReview belongs to a topic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic() {
        return $this->belongsTo('App\Topic');
    }

    /**
     * Each TopicReview has many TopicReviewReplies.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicReviewReplies() {
        return $this->hasMany('App\TopicReviewReply');
    }

    /**
     * Each TopicReview has many TopicReviewReviewers.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicReviewReviewers() {
        return $this->hasMany('App\TopicReviewReviewer');
    }


    /**
     * Each TopicReview has many Statuses.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicReviewStatus() {
        return $this->hasMany('App\TopicReviewStatus');
    }
}
