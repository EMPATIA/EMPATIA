<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashBoardElementUser extends Model
{
    use SoftDeletes;

    protected $table = 'dashboard_element_user';

    protected $fillable = ['dashboard_element_id','user_id','entity_id','position'];

    public function configurations() {
        return $this->belongsToMany('App\DashBoardElementConfiguration',
            'dashboard_element_user_configuration_pvt',
            'dashboard_element_user_id',
            'dashboard_element_configuration_id')->withPivot('value')->withTimestamps();

    }

    public function element() {
        return $this->belongsTo('App\DashboardElement');
    }
    public function user() {
        return $this->belongsTo('App\OrchUser');
    }
    public function entity() {
        return $this->belongsTo('App\Entity');
    }

}
