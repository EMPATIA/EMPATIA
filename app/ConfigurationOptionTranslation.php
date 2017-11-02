<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfigurationOptionTranslation extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['configuration_option_id', 'language_code', 'title', 'description', 'value'];

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
     * An Configuration Option Translation belongs to one Configuration Option
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function configurationOption(){
        return $this->belongsTo('App\ConfigurationOption');
    }
}
