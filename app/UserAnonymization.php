<?php

namespace App;

use Illuminate\Contracts\Console\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAnonymization extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'user_key',
        'user_anonymization_request_id'
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
    protected $hidden = ['deleted_at'];

    public function user(){
       return $this->hasOne('App\User','user_key','user_key');
    }

    public function orchUser(){
        return $this->belongsTo('App\OrchUser','user_key','user_key');
    }
    
    public function anonymizationRequest() {
        return $this->belongsTo('App\UserAnonymizationRequest','user_anonymization_request_id','id');
    }
}
