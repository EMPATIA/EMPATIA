<?php

namespace App;

use Illuminate\Contracts\Console\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAnonymizationRequest extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'process_status',
        'log',
        'created_by',
        'entity_key',
        'user_keys'
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

    public function anonymizer() {
        return $this->hasOne('App\User','user_key','created_by');
    }

    public function anonymizations() {
        return $this->hasMany('App\UserAnonymization');
    }

    public function entity(){
        return $this->belongsTo('App\Entity','entity_key','entity_key');
    }
}
