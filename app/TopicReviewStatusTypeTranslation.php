<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicReviewStatusTypeTranslation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_code',
        'topic_review_status_type_id',
        'name',
        'description'
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
     * A Topic Review Status Type Translation has many Topic Review Status Types.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicReviewStatusType() {
        return $this->hasMany('App\TopicReviewStatusType');
    }
}
