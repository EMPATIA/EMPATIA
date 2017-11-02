<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteConfTranslation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'lang_code',
        'site_conf_id',
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
     * SiteConfTranslation belongs to a SiteConf
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conf()
    {
        return $this->belongsTo("App\SiteConf");
    }


}

