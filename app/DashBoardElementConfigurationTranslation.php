<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashBoardElementConfigurationTranslation extends Model
{
    use SoftDeletes;

    protected $table = "dashboard_element_configuration_translations";
    protected $fillable = ['dashboard_element_configuration_id','title','description','language_code'];
}
