<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionTypeParameterTranslation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_code',
        'section_type_parameter_id',
        'name',
        'description'
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
     * A Section Type Parameter Translation belongs to one Section Type Parameter
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sectionTypeParameter(){
        return $this->belongsTo('App\SectionTypeParameter');
    }
}
