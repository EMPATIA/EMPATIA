<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParameterUserOptionTranslation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parameter_user_option_id', 'language_code', 'name'];

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
     * A Parameter User Option Translation belongs to one Parameter User Option
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parameterUserOption(){
        return $this->belongsTo('App\ParameterUserOption');
    }
}
