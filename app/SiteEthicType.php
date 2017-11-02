<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteEthicType extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_ethic_type_key',
        'code',
        'name'
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
    protected $hidden = ['id','deleted_at'];


    /**
     * Each Site Ethic Type has many Site Ethics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteEthics() {
        return $this->hasMany('App\SiteEthic');
    }
}
