<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Currency
 * @package App
 */
class Currency extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['currency', 'symbol_left', 'symbol_right', 'code', 'decimal_place', 'decimal_point', 'thousand_point'];

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

    /**
     * Currency can have many Entities
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entities(){
        return $this->hasMany('App\Entity');
    }
}
