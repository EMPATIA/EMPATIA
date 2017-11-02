<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CbNews extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cb_id', 'news_key','created_by','updated_by','tag'];


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
     * Each News belongs to a CB.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cb() {
        return $this->belongsTo('App\Cb');
    }
}
