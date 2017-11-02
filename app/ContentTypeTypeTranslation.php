<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentTypeTypeTranslation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_code',
        'content_type_type_id',
        'name'
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
     * A Content Type Type Translation belongs to one Content Type Type.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentTypeType(){
        return $this->belongsTo('App\ContentTypeType');
    }
}
