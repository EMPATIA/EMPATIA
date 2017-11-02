<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status_key',
        'status_type_id',
        'topic_id',
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
     * A Status belongs to a Topic
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic() {
        return $this->belongsTo('App\Topic');
    }

    /**
     * A Status belongs to a Status Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function statusType() {
        return $this->belongsTo('App\StatusType');
    }
    
    /**
     * A Status has many comments
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comments() {
        return $this->hasMany('App\Comment');
    }    
}
