<?php

namespace App\Modules\OpenData\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenDataEntity extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_by',
        'entity_key',
        'token'
    ];

    public function entity() {
        return $this->belongsTo('App\Entity','entity_key','entity_key');
    }

    public function userParameters() {
        return $this->hasMany('App\Modules\OpenData\Models\OpenDataUserParameter');
    }
    
    public function cbParameters() {
        return $this->hasMany('App\Modules\OpenData\Models\OpenDataCbParameter');
    }
    
    public function voteEvents() {
        return $this->hasMany('App\Modules\OpenData\Models\OpenDataVoteEvent');
    }

    public function creator() {
        return $this->hasOne('App\User','user_key','created_by');
    }

    public function exports() {
        return $this->hasMany('App\Modules\OpenData\Models\OpenDataExport');
    }
    public function currentExport() {
        return $this->hasOne('App\Modules\OpenData\Models\OpenDataExport')->orderByDesc("created_at");
    }
}
