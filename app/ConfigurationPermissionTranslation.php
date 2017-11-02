<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigurationPermissionTranslation extends Model
{
  use SoftDeletes;
/**
 * The attributes that are mass assignable.
 *
 * @var array
 */
protected $fillable = ['configuration_permission_id', 'language_code', 'title', 'description'];

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

public function configuration(){
    return $this->belongsTo('App\ConfigurationPermission');
}
}
