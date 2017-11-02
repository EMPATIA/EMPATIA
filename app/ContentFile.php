<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ContentFile
 * @package App
 */
class ContentFile extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content_id','file_id','name','description','position','type_id'];

    /**
     * Each file belongs to a Content
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content() {
        return $this->belongsTo('App\Content');
    }

}
