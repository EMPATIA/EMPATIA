<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicCb extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'cb_id',
        'topic_id',
        'created_by',
        'updated_by'
    ];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = ['deleted_at'];


    /**
     * Each Topic Cb belongs to one Topic
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cb(){
        return $this->belongsTo('App\Cb');
    }

    /**
     * Each Topic Cb belongs to one Cb
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic(){
        return $this->belongsTo('App\Topic');
    }
}
