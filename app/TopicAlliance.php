<?php

namespace App;

use Illuminate\Contracts\Console\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Topic Alliance
 * @package App
 */
class TopicAlliance extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ally_key',
        'request_message',
        'response_message',
        'accepted',
        'origin_topic_id',
        'destiny_topic_id',
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
     * Each alliance has a Origin topic
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function originTopic() {
        return $this->belongsTo('App\Topic',"origin_topic_id");
    }

    /**
     * Each alliance has a Destiny topic
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destinyTopic() {
        return $this->belongsTo('App\Topic',"destiny_topic_id");
    }
}