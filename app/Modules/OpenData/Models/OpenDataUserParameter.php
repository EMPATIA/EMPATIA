<?php

namespace App\Modules\OpenData\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenDataUserParameter extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_by',
        'parameter_user_type_key',
        'open_data_entity_id'
    ];

    public function openData() {
        return $this->belongsTo('App\Modules\OpenData\Models\OpenData');
    }
    
    public function parameterUserType() {
        return $this->belongsTo('App\ParameterUserType','parameter_user_type_key','parameter_user_type_key');
    }
}
