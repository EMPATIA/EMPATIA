<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterSubscription extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'newsletter_subscription_key',
        'email',
        'site_id',
        'active'
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
    protected $hidden = ['deleted_at', 'id'];

    /**
     * Each Newsletter Subscription belongs to one Site.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site() {
        return $this->belongsTo('App\Site');
    }
}
