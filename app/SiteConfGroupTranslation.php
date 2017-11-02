<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteConfGroupTranslation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'lang_code',
        'site_conf_group_id',
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
     * SiteConfGroupTranslation belongs to a SiteConfGroup
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function confGroup() {
        return $this->belongsTo("App\SiteConfGroup");
    }
}
