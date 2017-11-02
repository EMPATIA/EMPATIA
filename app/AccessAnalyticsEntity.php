<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessAnalyticsEntity extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'total_users',
        'total_page_access',
        'avg_page_per_second',
        'entity_id',
        'access_analytics_id'
    ];

    /**
     * A Access Analytics as one Access Analytics Entity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accessAnalytics(){
        return $this->belongsTo('App\AccessAnalytics');
    }
    
}
