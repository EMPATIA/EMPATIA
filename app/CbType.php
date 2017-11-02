<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CbType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'cb_type_key'

    ];

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
     * A CB Type has many Entity Cbs
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entityCbs(){
        return $this->hasMany('App\EntityCb');
    }

    public function entityCbTemplates(){
        return $this->hasMany('App\EntityCbTemplate');
    }
}
