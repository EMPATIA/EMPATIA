<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PostAbuse
 * @package App
 */
class PostAbuse extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['post_id','type_id','comment','created_by'];

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
     * This Post Abuse belongs to a Post.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post() {
        return $this->belongsTo('App\Post');
    }
}
