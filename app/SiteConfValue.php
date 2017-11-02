<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteConfValue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'site_conf_id',
        'value'
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
     * SiteConfValue belongs to Site
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site() {
        return $this->belongsTo("App\Site");
    }

    /*
     * SiteConfValue belongs to SiteConf
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siteConf() {
        return $this->belongsTo("App\SiteConf");
    }
}
