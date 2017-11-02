<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewContent extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content_key',
        'entity_key',
        'content_type_id',
        'content_type_type_id',
        'version',
        'code',
        'active',
        'name',
        'start_date',
        'end_date',
        'publish_date',
        'highlight',
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
     * Each Content may have many Content Sites
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contentSites() {
        return $this->hasMany('App\ContentSite', 'content_id');
    }

    /**
     * Each Content belongs to a Content Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentType() {
        return $this->belongsTo('App\ContentType');
    }

    /**
     * Each Content may belong to a Content Type Type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentTypeType() {
        return $this->belongsTo('App\ContentTypeType');
    }

    /**
     * Each Content has Many Sections
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections() {
        return $this->hasMany('App\Section', 'content_id');
    }
}
