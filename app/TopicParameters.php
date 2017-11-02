<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopicParameters extends Model
{
    /**
     * One topic parameter belongs to one topic version
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topicVersion(){
        return $this->belongsTo('App\TopicVersions');
    }
}
