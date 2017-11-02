<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnnotationTypeTranslation extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['annotation_type_id', 'language_code', 'value'];

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
     * An Annotation Type Translation belongs to one Annotation Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function annotationType(){
        return $this->belongsTo('App\AnnotationType');
    }
}
