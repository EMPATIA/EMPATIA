<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteEthicTranslation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_ethic_id',
        'language_code',
        'content'
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
     * Each Site Ethic Translation belongs to one Site Ethic
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siteEthic(){
        return $this->belongsTo('App\SiteEthic');
    }
}
