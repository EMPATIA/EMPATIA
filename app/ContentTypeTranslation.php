<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentTypeTranslation extends Model
{
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type_id', 'title', 'language_code'];

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
     * Each ContentTypeTranslation belongs to a ContentType
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentType() {
        return $this->belongsTo('App\ContentType');
    }
}
