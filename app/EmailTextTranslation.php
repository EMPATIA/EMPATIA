<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EmailTextTranslation
 * @package App
 */
class EmailTextTranslation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['subject', 'body', 'tag','email_text_id','created_by','updated_by','deleted_by'];
    
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
     * Each EmailTextTranlation belongs to an EmailText.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emailtext() {
        return $this->belongsTo('App\EmailText');
    }        

}
