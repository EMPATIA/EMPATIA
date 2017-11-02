<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content_id',
        'section_type_id',
        'section_key',
        'code'
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
     * Each Section belongs to a Content
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content() {
        return $this->belongsTo('App\NewContent');
    }

    /**
     * Each Section has Many Section Parameters
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sectionParameters() {
        return $this->hasMany('App\SectionParameter');
    }

    /**
     * Each Section belongs to a Section Types
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sectionType() {
        return $this->belongsTo('App\SectionType');
    }
}
