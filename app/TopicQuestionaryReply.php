<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopicQuestionaryReply extends Model
{
    protected $table = 'topic_questionary_reply';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'topic_key',
        'form_reply_key'
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
    protected $hidden = [
        'deleted_at',
        'id'
    ];
}
