<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParameterUserTypeTranslation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parameter_user_type_id', 'language_code', 'name', 'description'];

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
    protected $hidden = ['id', 'deleted_at'];

    /**
     * A Parameter User Type Translation belongs to one Parameter User Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parameterUserType(){
        return $this->belongsTo('App\ParameterUserType');
    }
}
