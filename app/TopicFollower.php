<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicFollower extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'topic_follower_key',
        'topic_id',
        'user_key'
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
     * Each TopicFollower belongs to a topic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic() {
        return $this->belongsTo('App\Topic');
    }

}
