<?php

namespace App;

use Illuminate\Contracts\Console\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicVersion extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'topic_id',
        'version',
        'active',
        'title',
        'contents',
        'summary',
        'description',
        'created_by',
        'active_by'
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
     * One topic version belongs to one topic
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic(){
       return $this->belongsTo('App\Topic');
    }

    /**
     * One topic version has many topic parameters
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topicParameters(){
        return $this->hasMany('App\TopicParameters');
    }


    public function topicParametersPivot(){
        return $this->belongsToMany('App\Parameter', 'topic_parameters')
            ->withPivot('value', 'topic_version_id')
            ->withTimestamps();
    }

}
