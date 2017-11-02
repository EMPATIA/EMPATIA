<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashBoardElementConfiguration extends Model
{
    use SoftDeletes;

    protected $table = "dashboard_element_configurations";
    protected $fillable = ['code','type','default_value'];

    public function translations() {
        return $this->hasMany('App\DashBoardElementConfigurationTranslation','dashboard_element_configuration_id');
    }
    public function newTranslation($language = null, $languageDefault = null) {
        $translation = $this->translations()->orderByRaw("FIELD(language_code,'".$languageDefault."','".$language."')DESC")->first();
        $this->setAttribute('title',$translation->title ?? "");
        $this->setAttribute('description',$translation->description ?? "");
        return true;
    }

    public function dashBoardElement(){
        return $this->hasMany('App\DashBoardElement');
    }
}
