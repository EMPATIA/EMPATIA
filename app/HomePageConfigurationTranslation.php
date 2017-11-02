<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomePageConfigurationTranslation extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['language_code', 'value'];

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
     * A Home Page Configuration Translation belongs to one Home Page Configuration
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function homePageConfiguration(){
        return $this->belongsTo('App\HomePageConfiguration');
    }
}
