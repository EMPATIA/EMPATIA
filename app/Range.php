<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Range extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['annotation_id', 'start', 'end', 'start_offset', 'end_offset'];

    /**
     * A Range belongs to an Annotation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function annotation() {
        return $this->belongsTo('App\Annotation');
    }
}
