<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ContentTranslation
 * @package App
 */
class ContentTranslation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content_id',
        'language_code',
        'title',
        'summary',
        'content',
        'link',
        'docs_main',
        'docs_side',
        'news',
        'events',
        'highlight',
        'slideshow',
        'created_by',
        'version',
        'enabled'
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
    protected $hidden = ['deleted_at'];
    
    /**
     * Each ContentType belongs to a Content
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content() {
        return $this->belongsTo('App\Content');
    }
}
