<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentSite extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content_id',
        'site_key'
    ];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = ['deleted_at'];

    /**
     * Each Content belongs to a Content Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content() {
        return $this->belongsTo('App\NewContent');
    }
}
