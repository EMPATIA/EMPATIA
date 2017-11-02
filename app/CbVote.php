<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CbVote extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cb_id', 'vote_key', 'created_by', 'vote_method', 'name'];

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
     * Each CbVote belongs to a Cb
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cb() {
        return $this->belongsTo('App\Cb');
    }

    public function voteConfigurations(){
        return $this->belongsToMany('App\VoteConfiguration', 'cb_vote_configurations')
            ->withPivot('value')
            ->withTimestamps();
    }
}
