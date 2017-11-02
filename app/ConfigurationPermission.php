<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigurationPermission extends Model
{
  use SoftDeletes;

/**
* The attributes that are mass assignable.
*
* @var array
*/
protected $fillable = ['code', 'configuration_permission_type_id', 'created_by'];

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

public function cbs()
{
  return $this->belongsToMany('App\Cb');
}

public function configurationPermissionType()
{
  return $this->belongsTo('App\ConfigurationPermissionType');
}

public function configurationPermissionTranslations(){
  return $this->hasMany('App\ConfigurationPermissionTranslation');
}

public function translation($language = null)
{
    $translation = $this->hasMany('App\ConfigurationPermissionTranslation')->where('language_code', '=', $language)->get();
    if(sizeof($translation)>0){
        $this->setAttribute('title',$translation[0]->title);
        $this->setAttribute('description',$translation[0]->description);
        return true;
    } else {
        return false;
    }
}
}
