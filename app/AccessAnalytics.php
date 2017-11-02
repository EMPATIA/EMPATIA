<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessAnalytics extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'total_users',
        'total_page_access',
        'avg_page_per_second',
        'created_by',
        'updated_by',
        'created_at'
    ];

    /**
     * A Access Analytics Entity has many Access Analytics
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessAnalyticsEntity(){
        return $this->hasMany('App\AccessAnalyticsEntity');
    }

}
