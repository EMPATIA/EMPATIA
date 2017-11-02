<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LevelParameter extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'level_parameter_key',
        'site_id',
        'mandatory',
        'manual_verification',
        'sms_verification',
        'name',
        'position'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = ['id'];

    /**
     * A Level Parameter belongs to many Users
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(){
        return $this->belongsToMany('App\OrchUser','level_parameter_user','level_parameter_id','user_id');
    }

    /**
     * A Level Parameter has many Parameter User Types
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parameterUserTypes(){
        return $this->hasMany('App\ParameterUserType');
    }

    /**
     * Each Level Parameter belongs to one Site.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site() {
        return $this->belongsTo('App\Site');
    }
}
