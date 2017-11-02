<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CbModerator
 * @package App
 */
class CbModerator extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cb_id', 'user_key','type_id','created_by','updated_by'];

    /**
     * Each Moderator belongs to a CB.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cb() {
        return $this->belongsTo('App\Cb');
    }
     
}