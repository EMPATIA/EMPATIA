<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicReviewReply extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'topic_review_reply_key',
        'topic_review_status_id',
        'topic_review_id',
        'content',
        'created_by'
    ];

    /**
     * Each TopicReviewReply belongs to a TopicReview.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topicReview() {
        return $this->belongsTo('App\TopicReview');
    }



}
