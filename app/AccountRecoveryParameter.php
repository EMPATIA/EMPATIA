<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountRecoveryParameter extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'table_key',
        'send_token',
        'entity_key',
        'parameter_user_type_key'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at','updated_at','deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];

    /**
     * An AccountRecoveryParameter belongs to one Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity(){
        return $this->belongsTo('App\Entity','entity_key','entity_key');
    }

    /**
     * An AccountRecoveryParameter belongs to one ParameterUserType.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parameterUserType(){
        return $this->belongsTo('App\ParameterUserType','parameter_user_type_key','parameter_user_type_key');
    }
}
