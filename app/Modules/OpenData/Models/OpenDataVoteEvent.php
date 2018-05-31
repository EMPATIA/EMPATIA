<?php

namespace App\Modules\OpenData\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenDataVoteEvent extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_by',
        'vote_event_key',
        'open_data_entity_id'
    ];

    public function openData() {
        return $this->belongsTo('App\Modules\OpenData\Models\OpenData');
    }
    public function vote() {
        return $this->belongsTo('App\CbVote','vote_event_key','vote_key');
    }
}
