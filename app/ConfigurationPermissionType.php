<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigurationPermissionType extends Model
{
  use SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['code', 'configuration_permission_id', 'created_by'];

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


  public function configurationPermission()
  {
      return $this->hasMany('App\ConfigurationPermission');
  }

  public function configurationPermissionTypeTranslations(){
      return $this->hasMany('App\ConfigurationPermissionTypeTranslation');
  }
}
