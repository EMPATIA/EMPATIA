<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteSiteConfs extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parameter_value',
        'site_conf_id',
        'site_id',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['start_date','end_date','deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['deleted_at'];

    /*
     * SiteSiteConfs belongs to a SiteConf
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siteConf() {
        return $this->belongsTo("App\SiteConf");
    }
}
