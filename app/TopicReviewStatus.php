<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicReviewStatus extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'topic_review_status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'topic_review_status_key',
        'topic_review_id',
        'topic_review_status_type_id',
        'active',
        'created_by'
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
     * A TopicReviewStatus belongs to a TopicReviewStatus Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topicReviewStatusType() {
        return $this->belongsTo('App\topicReviewStatusType');
    }


    /**
     * A Status belongs to a Topic
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topicReview() {
        return $this->belongsTo('App\TopicReview');
    }
}
