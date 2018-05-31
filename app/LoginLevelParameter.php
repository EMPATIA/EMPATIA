<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginLevelParameter extends Model
{
    use SoftDeletes;

    protected $table = 'login_level_parameter';

    protected $fillable = [
        'parameter_user_type_id',
        'login_level_id',
        'created_by',
        'updated_by'
    ];

  public function loginLevel() {
      return $this->belongsTo('App\LoginLevel');
  }


  public function parameterUserType() {
      return $this->belongsTo('App\ParameterUserType');
  }
}
