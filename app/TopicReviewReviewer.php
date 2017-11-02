<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopicReviewReviewer extends Model
{

    protected $fillable = [
        'topic_review_id',
        'reviewer_key',
        'is_group'
    ];

    /**
     * Each TopicReviewReviewer belongs to a TopicReview.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topicReview() {
        return $this->belongsTo('App\TopicReview');
    }
}
