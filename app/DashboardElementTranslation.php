<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashboardElementTranslation extends Model
{
    use SoftDeletes;

    protected $fillable = ['dashboard_element_id','title','description','language_code'];
}
