<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cooperator extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'topic_id',
        'user_key',
        'type_id',
        'created_by',
        'updated_by',
        'token'
    ];

    /**
     * Each CO-operator belongs to a Topic
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic() {
        return $this->belongsTo('App\Topic');
    }

    /**
     * This defines a many-to-many relationship between Cooperators and Topic Permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions() {
        return $this->belongsToMany('App\Permission');
    }

    /**
     * A Cooperator has many Annotations
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function annotations(){
        return $this->hasMany('App\Annotation');
    }

    /**
     * Each CO-operator belongs to a Cooperator Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cooperatorTypes(){
        return $this->belongsTo('App\CooperatorType');
    }

    /**
     * This defines a many-to-many relationship between Cooperators and Cooperations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cooperations() {
        return $this->belongsToMany('App\Cooperation')->withTimestamps();
    }

    /**
     * @return mixed
     */
    public function latestCooperation() {
        return $this->cooperations()->orderBy('pivot_created_at', 'desc')->first();
    }
}
