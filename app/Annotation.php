<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Annotation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['annotation_key', 'cooperator_id','post_id', 'text', 'quote'];

    /**
     * An Annotation belongs to a Cooperator
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cooperator() {
        return $this->belongsTo('App\Cooperator');
    }

    /**
     * An Annotation belongs to a Post
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post() {
        return $this->belongsTo('App\Post');
    }

    /**
     * A Cooperator has many Ranges
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ranges(){
        return $this->hasMany('App\Range');
    }

    /**
     * This defines a many-to-many relationship between Annotations and Annotation Types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function annotationTypes() {
        return $this->belongsToMany('App\AnnotationType');
    }
}
