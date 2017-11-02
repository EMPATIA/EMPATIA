<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntityDashBoardElement extends Model
{
    use SoftDeletes;

    protected $table = 'entity_dashboard_element';

    protected $fillable = ['dashboard_element_id','entity_id','position'];

    public function configurations() {
        return $this->hasMany('App\DashBoardElementConfiguration');
    }
}
