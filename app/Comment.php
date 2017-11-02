<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment_key',
        'status_id',
        'public',
        'title',
        'content',
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
     * A Comment belongs to a Status
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status() {
        return $this->belongsTo('App\Status');
    }

    /**
     * Return the model with dates converted to entity timezone
     *
     * @param $request
     * @return $this
     */
    public function timezone($request) {
        $timezone = empty($request->header('timezone')) ? 'utc' : $request->header('timezone');

        $this->created_at =  is_null($this->created_at) ? null : $this->created_at->timezone($timezone);
        $this->updated_at =  is_null($this->updated_at) ? null : $this->updated_at->timezone($timezone);
        $this->deleted_at =  is_null($this->deleted_at) ? null : $this->deleted_at->timezone($timezone);

        return $this;
    }
}
