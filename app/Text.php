<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Text
 * @package App
 */
class Text extends Model
{
    use SoftDeletes;
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key'];

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
     * Each Text has many TextTranslations.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations() {
        return $this->hasMany('App\TextTranslation');
    }
}
