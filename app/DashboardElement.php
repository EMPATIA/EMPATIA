<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashboardElement extends Model
{
    use SoftDeletes;

    protected $fillable = ['code','position'];

    public function translations() {
        return $this->hasMany('App\DashboardElementTranslation');
    }
    public function newTranslation($language = null, $languageDefault = null) {
        $translation = $this->translations()->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->first();
        $this->setAttribute('title',$translation->title ?? "");
        $this->setAttribute('description',$translation->description ?? "");
    }

    public function configurations() {
        return $this->belongsToMany('App\DashBoardElementConfiguration',
            'dashboard_element_configuration_pvt',
            'dashboard_element_id',
            'dashboard_element_configuration_id')->withPivot('default_value')->withTimestamps();

    }
}
