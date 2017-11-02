<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TextTranslation
 * @package App
 */
class TextTranslation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'content','text_id','tag','created_by','updated_by','deleted_by'];

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
     * Each TextTranlation belongs to a Text.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function text() {
        return $this->belongsTo('App\Text');
    }    

}
