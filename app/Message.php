<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'message_key',
        'user_id',
        'entity_id',
        'topic_id',
        'value',
        'to',
        'from',
        'viewed',
        'viewed_at',
        'viewed_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = ['id', 'deleted_at'];

    /**
     * Each Message to one User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('App\OrchUser','user_id');
    }

    /**
     * Each Message to one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity() {
        return $this->belongsTo('App\Entity');
    }

    /**
     * Return the model with dates converted to entity timezone
     *
     * @param $request
     * @return $this
     */
    public function timezone($request) {

        $timezone = $request->name;

        $this->created_at =  is_null($this->created_at) ? null : $this->created_at->timezone($timezone);
        $this->updated_at =  is_null($this->updated_at) ? null : $this->updated_at->timezone($timezone);
        $this->deleted_at =  is_null($this->deleted_at) ? null : $this->deleted_at->timezone($timezone);

        return $this;
    }

    public function sender() {
        return $this->belongsTo("App\User","from","user_key");
    }

    public function receiver() {
        return $this->belongsTo("App\User","to","user_key");
    }
}
