<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLoginLevel extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'user_id',
        'login_level_id',
        'created_by',
        'updated_by',
        'manual'
    ];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = ['deleted_at'];


    /**
     * Each Login Level User belongs to one User
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\OrchUser', 'orch_user_id');
    }

    /**
     * Each Login Level User belongs to one Login Level
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loginLevel(){
        return $this->belongsTo('App\LoginLevel','login_level_id');
    }
}
